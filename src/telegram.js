// ادغام تلگرام: ارسال پیام و پردازش وبهوک ربات
import { getSetting } from './config.js';

export async function tgSend(env, chatId, text) {
  const botToken = await getSetting(env, 'TELEGRAM_BOT_TOKEN');
  if (!chatId || !botToken) return;
  try {
    await fetch(`https://api.telegram.org/bot${botToken}/sendMessage`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ chat_id: chatId, text, parse_mode: 'HTML' }),
    });
  } catch (e) {
    console.error('telegram send error', e);
  }
}

export async function notifyAllAdmins(env, text, excludeAdminId = null) {
  const { results } = await env.DB.prepare(
    'SELECT id, telegram_chat_id FROM admins WHERE telegram_chat_id IS NOT NULL'
  ).all();
  for (const a of results) {
    if (excludeAdminId && a.id === excludeAdminId) continue;
    await tgSend(env, a.telegram_chat_id, text);
  }
}

export async function handleTelegramWebhook(request, env) {
  let update;
  try {
    update = await request.json();
  } catch {
    return new Response('bad request', { status: 400 });
  }

  const msg = update.message;
  if (!msg || !msg.text) return new Response('ok');

  const chatId = msg.chat.id;
  const text = msg.text.trim();

  if (text.startsWith('/start')) {
    const parts = text.split(/\s+/);
    const code = parts[1];
    if (code) {
      const admin = await env.DB.prepare('SELECT * FROM admins WHERE telegram_link_code = ?')
        .bind(code)
        .first();
      if (admin) {
        await env.DB.prepare(
          'UPDATE admins SET telegram_chat_id = ?, telegram_link_code = NULL WHERE id = ?'
        )
          .bind(String(chatId), admin.id)
          .run();
        await tgSend(env, chatId, `✅ حساب «${admin.name}» با موفقیت به دکتر خسته وصل شد.\nاز این پس یادآوری موعد تسک‌ها اینجا ارسال می‌شود.\n\nدستورات: /tasks برای دیدن تسک‌های باز`);
      } else {
        await tgSend(env, chatId, '❌ کد نامعتبر یا منقضی‌شده است. یک کد جدید از داشبورد (بخش تنظیمات تلگرام) بگیرید.');
      }
    } else {
      await tgSend(
        env,
        chatId,
        '👋 سلام! به ربات «دکتر خسته» خوش آمدید.\n\nبرای اتصال حساب ادمین خودتان، وارد داشبورد شوید، از بخش «اتصال تلگرام» یک کد بگیرید و اینجا به‌صورت زیر ارسال کنید:\n/start CODE'
      );
    }
    return new Response('ok');
  }

  if (text === '/tasks' || text === '/تسکها' || text === '/تسک‌ها') {
    const admin = await env.DB.prepare('SELECT * FROM admins WHERE telegram_chat_id = ?')
      .bind(String(chatId))
      .first();
    if (!admin) {
      await tgSend(env, chatId, 'حساب شما هنوز وصل نیست. ابتدا /start CODE را ارسال کنید.');
      return new Response('ok');
    }
    const { results } = await env.DB.prepare(
      "SELECT * FROM tasks WHERE status != 'done' ORDER BY (due_date IS NULL), due_date ASC LIMIT 15"
    ).all();
    if (!results.length) {
      await tgSend(env, chatId, '🎉 هیچ تسک بازی وجود ندارد!');
      return new Response('ok');
    }
    const icons = { urgent: '🔴', high: '🟠', normal: '🟡', low: '⚪️' };
    let out = '📋 <b>تسک‌های باز:</b>\n\n';
    for (const t of results) {
      out += `${icons[t.priority] || '⚪️'} ${t.title}${t.due_date ? '  —  موعد: ' + t.due_date : ''}\n`;
    }
    await tgSend(env, chatId, out);
    return new Response('ok');
  }

  if (text === '/help' || text === '/راهنما') {
    await tgSend(env, chatId, 'دستورات:\n/start CODE — اتصال حساب\n/tasks — نمایش تسک‌های باز');
  }

  return new Response('ok');
}
