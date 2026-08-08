import {
  hashPassword,
  verifyPassword,
  randomHex,
  getCookie,
  sessionCookieHeader,
  clearSessionCookieHeader,
  createSession,
  destroySession,
  getSessionAdmin,
  publicAdmin,
} from './auth.js';
import { tgSend, tgSendInline, notifyAllAdmins, notifyAllAdminsInline, handleTelegramWebhook } from './telegram.js';
import { backupDatabaseToGithub, uploadFileToGithub } from './github.js';
import { getSetting, setSetting } from './config.js';

function json(data, status = 200, extraHeaders = {}) {
  return new Response(JSON.stringify(data), {
    status,
    headers: { 'Content-Type': 'application/json; charset=utf-8', ...extraHeaders },
  });
}

function err(message, status = 400) {
  return json({ error: message }, status);
}

async function readJson(request) {
  try {
    return await request.json();
  } catch {
    return {};
  }
}

function todayISO() {
  return new Date().toISOString().slice(0, 10);
}

function addDaysISO(dateStr, days) {
  const d = new Date(dateStr + 'T00:00:00Z');
  d.setUTCDate(d.getUTCDate() + days);
  return d.toISOString().slice(0, 10);
}

export default {
  async fetch(request, env, ctx) {
    const url = new URL(request.url);
    const { pathname } = url;

    try {
      // --- وبهوک تلگرام (بدون نیاز به احراز هویت داشبورد، محافظت‌شده با راز در مسیر) ---
      const webhookSecret = await getSetting(env, 'TELEGRAM_WEBHOOK_SECRET');
      if (webhookSecret && pathname === `/telegram/webhook/${webhookSecret}` && request.method === 'POST') {
        return await handleTelegramWebhook(request, env);
      }

      if (pathname.startsWith('/api/')) {
        return await handleApi(request, env, url);
      }

      // بقیه مسیرها: فایل‌های استاتیک داشبورد
      return env.ASSETS.fetch(request);
    } catch (e) {
      console.error(e);
      return err('خطای داخلی سرور: ' + e.message, 500);
    }
  },

  async scheduled(event, env, ctx) {
    const backupCron = '0 2 * * *';
    if (event.cron === backupCron) {
      ctx.waitUntil(backupDatabaseToGithub(env).catch((e) => console.error('backup failed', e)));
      return;
    }
    ctx.waitUntil(checkDueTasksAndNotify(env));
  },
};

async function checkDueTasksAndNotify(env) {
  const today = todayISO();
  const tomorrow = addDaysISO(today, 1);

  const { results } = await env.DB.prepare(
    `SELECT tasks.*, a.telegram_chat_id as assignee_chat, a.name as assignee_name
     FROM tasks LEFT JOIN admins a ON a.id = tasks.assigned_to
     WHERE tasks.status != 'done' AND tasks.due_date IS NOT NULL`
  ).all();

  for (const t of results) {
    const recipients = [];
    if (t.assignee_chat) recipients.push(t.assignee_chat);
    else {
      const { results: all } = await env.DB.prepare(
        'SELECT telegram_chat_id FROM admins WHERE telegram_chat_id IS NOT NULL'
      ).all();
      for (const a of all) recipients.push(a.telegram_chat_id);
    }

    if (t.due_date === tomorrow && !t.notified_1day) {
      for (const chat of recipients) await tgSend(env, chat, `⏰ یادآوری: فردا موعد تسک «${t.title}» است.`);
      await env.DB.prepare('UPDATE tasks SET notified_1day = 1 WHERE id = ?').bind(t.id).run();
    } else if (t.due_date === today && !t.notified_due) {
      for (const chat of recipients) await tgSend(env, chat, `📌 امروز موعد تسک «${t.title}» است!`);
      await env.DB.prepare('UPDATE tasks SET notified_due = 1 WHERE id = ?').bind(t.id).run();
    } else if (t.due_date < today && !t.notified_overdue) {
      for (const chat of recipients)
        await tgSend(env, chat, `⚠️ تسک «${t.title}» از موعد (${t.due_date}) گذشته و هنوز انجام نشده.`);
      await env.DB.prepare('UPDATE tasks SET notified_overdue = 1 WHERE id = ?').bind(t.id).run();
    }
  }
}

async function handleApi(request, env, url) {
  const path = url.pathname.replace(/^\/api/, '');
  const method = request.method;

  // --- راه‌اندازی اولیه: فقط وقتی هیچ ادمینی وجود ندارد ---
  if (path === '/setup/status' && method === 'GET') {
    const row = await env.DB.prepare('SELECT COUNT(*) as c FROM admins').first();
    return json({ needsSetup: row.c === 0 });
  }

  if (path === '/setup' && method === 'POST') {
    const row = await env.DB.prepare('SELECT COUNT(*) as c FROM admins').first();
    if (row.c > 0) return err('راه‌اندازی قبلاً انجام شده است.', 403);
    const { username, password, name, color } = await readJson(request);
    if (!username || !password || !name) return err('نام کاربری، رمز عبور و نام الزامی است.');
    const salt = randomHex(16);
    const hash = await hashPassword(password, salt);
    const result = await env.DB.prepare(
      'INSERT INTO admins (username, password_hash, salt, name, color, is_super) VALUES (?,?,?,?,?,1)'
    )
      .bind(username, hash, salt, name, color || '#f2a154')
      .run();
    const adminId = result.meta.last_row_id;
    const { token, maxAge } = await createSession(env, adminId);
    const admin = await env.DB.prepare('SELECT * FROM admins WHERE id = ?').bind(adminId).first();
    return json({ admin: publicAdmin(admin) }, 200, { 'Set-Cookie': sessionCookieHeader(token, maxAge) });
  }

  // --- ورود / خروج ---
  if (path === '/login' && method === 'POST') {
    const { username, password } = await readJson(request);
    const admin = await env.DB.prepare('SELECT * FROM admins WHERE username = ?').bind(username).first();
    if (!admin) return err('نام کاربری یا رمز عبور اشتباه است.', 401);
    const ok = await verifyPassword(password || '', admin.salt, admin.password_hash);
    if (!ok) return err('نام کاربری یا رمز عبور اشتباه است.', 401);
    const { token, maxAge } = await createSession(env, admin.id);
    return json({ admin: publicAdmin(admin) }, 200, { 'Set-Cookie': sessionCookieHeader(token, maxAge) });
  }

  if (path === '/logout' && method === 'POST') {
    const token = getCookie(request, 'session');
    await destroySession(env, token);
    return json({ ok: true }, 200, { 'Set-Cookie': clearSessionCookieHeader() });
  }

  // --- از این‌جا به بعد نیاز به ورود دارد ---
  const me = await getSessionAdmin(request, env);
  if (path === '/me' && method === 'GET') {
    if (!me) return err('وارد نشده‌اید.', 401);
    return json({ admin: publicAdmin(me) });
  }
  if (!me) return err('وارد نشده‌اید.', 401);

  // --- مدیریت داینامیک تنظیمات از داخل خود اپ (فقط برای ادمین اصلی) ---
  const settingsKeys = [
    'TELEGRAM_BOT_TOKEN',
    'TELEGRAM_WEBHOOK_SECRET',
    'TELEGRAM_BOT_USERNAME',
    'GITHUB_TOKEN',
    'GITHUB_REPO',
    'GITHUB_BRANCH',
    'GITHUB_BACKUP_DIR',
    'GITHUB_FILES_DIR'
  ];

  if (path === '/settings' && method === 'GET') {
    if (!me.is_super) return err('فقط ادمین اصلی به تنظیمات پیشرفته سیستم دسترسی دارد.', 403);
    const settings = {};
    for (const key of settingsKeys) {
      settings[key] = await getSetting(env, key);
    }
    return json({ settings });
  }

  if (path === '/settings' && method === 'POST') {
    if (!me.is_super) return err('فقط ادمین اصلی به تنظیمات پیشرفته سیستم دسترسی دارد.', 403);
    const body = await readJson(request);
    for (const key of settingsKeys) {
      if (body[key] !== undefined) {
        await setSetting(env, key, body[key].trim());
      }
    }
    return json({ ok: true });
  }

  // --- میز کار و گفتگوی تیمی (بدون قابلیت حذف و ویرایش) ---
  if (path === '/messages' && method === 'GET') {
    try {
      const { results } = await env.DB.prepare(
        `SELECT messages.*, admins.name as sender_name, admins.color as sender_color
         FROM messages
         JOIN admins ON admins.id = messages.admin_id
         ORDER BY messages.created_at ASC LIMIT 100`
      ).all();
      return json({ messages: results });
    } catch (e) {
      // خودبهبودی جدول در دیتابیس قدیمی
      await env.DB.prepare(
        `CREATE TABLE IF NOT EXISTS messages (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          admin_id INTEGER NOT NULL,
          content TEXT NOT NULL,
          created_at TEXT NOT NULL DEFAULT (datetime('now')),
          FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
        )`
      ).run().catch(()=>{});
      return json({ messages: [] });
    }
  }

  if (path === '/messages' && method === 'POST') {
    const { content } = await readJson(request);
    if (!content || !content.trim()) return err('متن پیام نمی‌تواند خالی باشد.');
    try {
      await env.DB.prepare('INSERT INTO messages (admin_id, content) VALUES (?, ?)')
        .bind(me.id, content.trim())
        .run();
      return json({ ok: true });
    } catch (e) {
      await env.DB.prepare(
        `CREATE TABLE IF NOT EXISTS messages (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          admin_id INTEGER NOT NULL,
          content TEXT NOT NULL,
          created_at TEXT NOT NULL DEFAULT (datetime('now')),
          FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
        )`
      ).run().catch(()=>{});
      await env.DB.prepare('INSERT INTO messages (admin_id, content) VALUES (?, ?)')
        .bind(me.id, content.trim())
        .run();
      return json({ ok: true });
    }
  }

  // --- ادمین‌ها ---
  if (path === '/admins' && method === 'GET') {
    const { results } = await env.DB.prepare('SELECT id, username, name, color, is_super, telegram_chat_id, created_at FROM admins ORDER BY created_at ASC').all();
    return json({ admins: results.map(publicAdmin) });
  }

  if (path === '/admins' && method === 'POST') {
    if (!me.is_super) return err('فقط ادمین اصلی می‌تواند ادمین بسازد.', 403);
    const { username, password, name, color, telegram_chat_id } = await readJson(request);
    if (!username || !password || !name) return err('نام کاربری، رمز عبور و نام الزامی است.');
    const exists = await env.DB.prepare('SELECT id FROM admins WHERE username = ?').bind(username).first();
    if (exists) return err('این نام کاربری قبلاً استفاده شده است.');
    const salt = randomHex(16);
    const hash = await hashPassword(password, salt);
    const result = await env.DB.prepare(
      'INSERT INTO admins (username, password_hash, salt, name, color, telegram_chat_id, is_super) VALUES (?,?,?,?,?,?,0)'
    )
      .bind(username, hash, salt, name, color || '#4fd1c5', telegram_chat_id || null)
      .run();
    const admin = await env.DB.prepare('SELECT * FROM admins WHERE id = ?').bind(result.meta.last_row_id).first();
    return json({ admin: publicAdmin(admin) });
  }

  const adminIdMatch = path.match(/^\/admins\/(\d+)$/);
  if (adminIdMatch && method === 'PUT') {
    const targetId = Number(adminIdMatch[1]);
    if (targetId !== me.id && !me.is_super) return err('اجازه ندارید.', 403);
    const { name, color, password, telegram_chat_id } = await readJson(request);
    const fields = [];
    const binds = [];
    if (name) {
      fields.push('name = ?');
      binds.push(name);
    }
    if (color) {
      fields.push('color = ?');
      binds.push(color);
    }
    if (password) {
      const salt = randomHex(16);
      const hash = await hashPassword(password, salt);
      fields.push('salt = ?', 'password_hash = ?');
      binds.push(salt, hash);
    }
    if (telegram_chat_id !== undefined) {
      fields.push('telegram_chat_id = ?');
      binds.push(telegram_chat_id ? telegram_chat_id.trim() : null);
    }
    if (!fields.length) return err('چیزی برای تغییر ارسال نشده.');
    binds.push(targetId);
    await env.DB.prepare(`UPDATE admins SET ${fields.join(', ')} WHERE id = ?`).bind(...binds).run();
    const admin = await env.DB.prepare('SELECT * FROM admins WHERE id = ?').bind(targetId).first();
    return json({ admin: publicAdmin(admin) });
  }

  if (adminIdMatch && method === 'DELETE') {
    if (!me.is_super) return err('فقط ادمین اصلی می‌تواند ادمین حذف کند.', 403);
    const targetId = Number(adminIdMatch[1]);
    if (targetId === me.id) return err('نمی‌توانید خودتان را حذف کنید.');
    await env.DB.prepare('DELETE FROM admins WHERE id = ?').bind(targetId).run();
    return json({ ok: true });
  }

  // --- تسک‌ها ---
  if (path === '/tasks' && method === 'GET') {
    const { results } = await env.DB.prepare(
      `SELECT tasks.*, c.name as creator_name, c.color as creator_color,
              a.name as assignee_name, a.color as assignee_color
       FROM tasks
       LEFT JOIN admins c ON c.id = tasks.created_by
       LEFT JOIN admins a ON a.id = tasks.assigned_to
       ORDER BY (tasks.due_date IS NULL), tasks.due_date ASC, tasks.created_at DESC`
    ).all();
    return json({ tasks: results });
  }

  if (path === '/tasks' && method === 'POST') {
    const { title, description, due_date, priority, assigned_to } = await readJson(request);
    if (!title) return err('عنوان تسک الزامی است.');
    const result = await env.DB.prepare(
      `INSERT INTO tasks (title, description, due_date, priority, created_by, assigned_to)
       VALUES (?,?,?,?,?,?)`
    )
      .bind(title, description || '', due_date || null, priority || 'normal', me.id, assigned_to || null)
      .run();
    const taskId = result.meta.last_row_id;

    // ارسال نوتیفیکیشن دکمه‌های شیشه‌ای تعاملی به تلگرام
    const notifyText = `🆕 <b>تسک جدید توسط ${me.name}:</b>\n«${title}»${due_date ? '\nموعد: ' + due_date : ''}`;
    const replyMarkup = {
      inline_keyboard: [
        [
          { text: '✅ تکمیل تسک', callback_data: `complete:${taskId}` },
          { text: '🔄 در حال انجام', callback_data: `inprogress:${taskId}` }
        ],
        [
          { text: '🙋‍♂️ واگذاری به من', callback_data: `assign:${taskId}` }
        ]
      ]
    };

    let chatTarget = null;
    if (assigned_to) {
      const a = await env.DB.prepare('SELECT telegram_chat_id FROM admins WHERE id = ?').bind(assigned_to).first();
      chatTarget = a && a.telegram_chat_id;
    }
    if (chatTarget) await tgSendInline(env, chatTarget, notifyText, replyMarkup);
    else await notifyAllAdminsInline(env, notifyText, replyMarkup, me.id);

    return json({ id: taskId });
  }

  const taskIdMatch = path.match(/^\/tasks\/(\d+)$/);
  if (taskIdMatch && method === 'PUT') {
    const id = Number(taskIdMatch[1]);
    const { title, description, due_date, status, priority, assigned_to } = await readJson(request);
    const fields = [];
    const binds = [];
    if (title !== undefined) { fields.push('title = ?'); binds.push(title); }
    if (description !== undefined) { fields.push('description = ?'); binds.push(description); }
    if (status !== undefined) { fields.push('status = ?'); binds.push(status); }
    if (priority !== undefined) { fields.push('priority = ?'); binds.push(priority); }
    if (assigned_to !== undefined) { fields.push('assigned_to = ?'); binds.push(assigned_to); }
    if (due_date !== undefined) {
      fields.push('due_date = ?', 'notified_1day = 0', 'notified_due = 0', 'notified_overdue = 0');
      binds.push(due_date);
    }
    fields.push("updated_at = datetime('now')");
    if (!fields.length) return err('چیزی برای تغییر ارسال نشده.');
    binds.push(id);
    await env.DB.prepare(`UPDATE tasks SET ${fields.join(', ')} WHERE id = ?`).bind(...binds).run();

    if (status === 'done') {
      const t = await env.DB.prepare('SELECT title FROM tasks WHERE id = ?').bind(id).first();
      await notifyAllAdmins(env, `✅ تسک «${t.title}» توسط ${me.name} تکمیل شد.`, me.id);
    }
    return json({ ok: true });
  }

  if (taskIdMatch && method === 'DELETE') {
    await env.DB.prepare('DELETE FROM tasks WHERE id = ?').bind(Number(taskIdMatch[1])).run();
    return json({ ok: true });
  }

  // --- یادداشت‌ها ---
  if (path === '/notes' && method === 'GET') {
    const { results } = await env.DB.prepare(
      `SELECT notes.*, admins.name as creator_name, admins.color as creator_color
       FROM notes LEFT JOIN admins ON admins.id = notes.created_by
       ORDER BY notes.created_at DESC`
    ).all();
    return json({ notes: results });
  }

  if (path === '/notes' && method === 'POST') {
    const { title, content, color } = await readJson(request);
    if (!title) return err('عنوان یادداشت الزامی است.');
    const result = await env.DB.prepare(
      'INSERT INTO notes (title, content, color, created_by) VALUES (?,?,?,?)'
    )
      .bind(title, content || '', color || me.color, me.id)
      .run();
    return json({ id: result.meta.last_row_id });
  }

  const noteIdMatch = path.match(/^\/notes\/(\d+)$/);
  if (noteIdMatch && method === 'PUT') {
    const id = Number(noteIdMatch[1]);
    const { title, content, color } = await readJson(request);
    const fields = [];
    const binds = [];
    if (title !== undefined) { fields.push('title = ?'); binds.push(title); }
    if (content !== undefined) { fields.push('content = ?'); binds.push(content); }
    if (color !== undefined) { fields.push('color = ?'); binds.push(color); }
    fields.push("updated_at = datetime('now')");
    binds.push(id);
    await env.DB.prepare(`UPDATE notes SET ${fields.join(', ')} WHERE id = ?`).bind(...binds).run();
    return json({ ok: true });
  }

  if (noteIdMatch && method === 'DELETE') {
    await env.DB.prepare('DELETE FROM notes WHERE id = ?').bind(Number(noteIdMatch[1])).run();
    return json({ ok: true });
  }

  // --- اتصال تلگرام ---
  if (path === '/telegram/link-code' && method === 'POST') {
    const code = randomHex(6);
    await env.DB.prepare('UPDATE admins SET telegram_link_code = ? WHERE id = ?').bind(code, me.id).run();
    const botUsername = await getSetting(env, 'TELEGRAM_BOT_USERNAME');
    return json({ code, botUsername: botUsername || null });
  }

  if (path === '/telegram/unlink' && method === 'POST') {
    await env.DB.prepare('UPDATE admins SET telegram_chat_id = NULL WHERE id = ?').bind(me.id).run();
    return json({ ok: true });
  }

  // --- بکاپ دستی به گیت‌هاب ---
  if (path === '/backup' && method === 'POST') {
    try {
      const path_ = await backupDatabaseToGithub(env);
      return json({ ok: true, path: path_ });
    } catch (e) {
      return err(e.message, 500);
    }
  }

  // --- آپلود فایل (base64) به گیت‌هاب ---
  if (path === '/upload' && method === 'POST') {
    const { filename, dataBase64 } = await readJson(request);
    if (!filename || !dataBase64) return err('فایل نامعتبر است.');
    try {
      const clean = dataBase64.includes(',') ? dataBase64.split(',')[1] : dataBase64;
      const { url } = await uploadFileToGithub(env, filename, clean);
      return json({ url });
    } catch (e) {
      return err(e.message, 500);
    }
  }

  return err('یافت نشد.', 404);
}
