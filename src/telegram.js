// ادغام تلگرام: ارسال پیام و پردازش وبهوک ربات با اتصال مستقیم و سریع آیدی عددی
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

export async function tgSendInline(env, chatId, text, replyMarkup) {
  const botToken = await getSetting(env, 'TELEGRAM_BOT_TOKEN');
  if (!chatId || !botToken) return;
  try {
    await fetch(`https://api.telegram.org/bot${botToken}/sendMessage`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ chat_id: chatId, text, parse_mode: 'HTML', reply_markup: replyMarkup }),
    });
  } catch (e) {
    console.error('telegram send inline error', e);
  }
}

export async function answerCallbackQuery(env, callbackQueryId, text) {
  const botToken = await getSetting(env, 'TELEGRAM_BOT_TOKEN');
  if (!botToken) return;
  try {
    await fetch(`https://api.telegram.org/bot${botToken}/answerCallbackQuery`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ callback_query_id: callbackQueryId, text, show_alert: true }),
    });
  } catch (e) {
    console.error('telegram answerCallbackQuery error', e);
  }
}

export async function editMessageText(env, chatId, messageId, text) {
  const botToken = await getSetting(env, 'TELEGRAM_BOT_TOKEN');
  if (!botToken) return;
  try {
    await fetch(`https://api.telegram.org/bot${botToken}/editMessageText`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ chat_id: chatId, message_id: messageId, text, parse_mode: 'HTML' }),
    });
  } catch (e) {
    console.error('telegram editMessageText error', e);
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

export async function notifyAllAdminsInline(env, text, replyMarkup, excludeAdminId = null) {
  const { results } = await env.DB.prepare(
    'SELECT id, telegram_chat_id FROM admins WHERE telegram_chat_id IS NOT NULL'
  ).all();
  for (const a of results) {
    if (excludeAdminId && a.id === excludeAdminId) continue;
    await tgSendInline(env, a.telegram_chat_id, text, replyMarkup);
  }
}

export async function handleTelegramWebhook(request, env) {
  let update;
  try {
    update = await request.json();
  } catch {
    return new Response('bad request', { status: 400 });
  }

  // --- هندل کردن دکمه‌های شیشه‌ای (Callback Queries) ---
  const cb = update.callback_query;
  if (cb) {
    const fromId = cb.from.id;
    const data = cb.data;
    const callbackQueryId = cb.id;

    // پیدا کردن ادمین بر اساس آیدی عددی تلگرام به صورت مستقیم
    const admin = await env.DB.prepare('SELECT * FROM admins WHERE telegram_chat_id = ?')
      .bind(String(fromId))
      .first();

    if (!admin) {
      await answerCallbackQuery(env, callbackQueryId, `❌ حساب تلگرام شما هنوز در داشبورد ست نشده است. آیدی شما: ${fromId}`);
      return new Response('ok');
    }

    const parts = data.split(':');
    const action = parts[0];
    const taskId = parseInt(parts[1], 10);

    const task = await env.DB.prepare('SELECT * FROM tasks WHERE id = ?').bind(taskId).first();
    if (!task) {
      await answerCallbackQuery(env, callbackQueryId, '❌ این تسک یافت نشد یا ممکن است حذف شده باشد.');
      return new Response('ok');
    }

    if (action === 'complete') {
      await env.DB.prepare("UPDATE tasks SET status = 'done', updated_at = datetime('now') WHERE id = ?").bind(taskId).run();
      await answerCallbackQuery(env, callbackQueryId, '✅ تسک تکمیل و ثبت شد.');
      await editMessageText(env, cb.message.chat.id, cb.message.message_id, `✅ <b>تسک تکمیل شد:</b>\n<s>${task.title}</s>\n\nتکمیل‌کننده: <b>${admin.name}</b>`);
    } else if (action === 'inprogress') {
      await env.DB.prepare("UPDATE tasks SET status = 'in_progress', updated_at = datetime('now') WHERE id = ?").bind(taskId).run();
      await answerCallbackQuery(env, callbackQueryId, '🔄 تسک به وضعیت در حال انجام تغییر کرد.');
      await editMessageText(env, cb.message.chat.id, cb.message.message_id, `🔄 <b>تسک در حال انجام است:</b>\n«${task.title}»\n\nمسئول شروع: <b>${admin.name}</b>`);
    } else if (action === 'assign') {
      await env.DB.prepare("UPDATE tasks SET assigned_to = ?, updated_at = datetime('now') WHERE id = ?").bind(admin.id, taskId).run();
      await answerCallbackQuery(env, callbackQueryId, '🙋‍♂️ تسک به شما واگذار شد.');
      await editMessageText(env, cb.message.chat.id, cb.message.message_id, `🙋‍♂️ <b>تسک واگذار شد به:</b>\n«${task.title}»\n\nمسئول جدید: <b>${admin.name}</b>`);
    }
    return new Response('ok');
  }

  const msg = update.message;
  if (!msg || !msg.text) return new Response('ok');

  const chatId = msg.chat.id;
  const text = msg.text.trim();

  // پیدا کردن ادمین بر اساس آیدی عددی تلگرام به صورت مستقیم
  const admin = await env.DB.prepare('SELECT * FROM admins WHERE telegram_chat_id = ?')
    .bind(String(chatId))
    .first();

  if (admin) {
    if (text.startsWith('/start') || text === '/help' || text === '/راهنما') {
      await tgSend(
        env,
        chatId,
        `👋 سلام <b>${admin.name}</b> عزیز!\n\nحساب تلگرام شما با موفقیت متصل است و پیام‌های ربات دکتر خسته را دریافت می‌کنید.\n\n📋 دستورات:\n/tasks — نمایش لیست تسک‌های باز`
      );
      return new Response('ok');
    }

    if (text === '/tasks' || text === '/تسکها' || text === '/تسک‌ها') {
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
  } else {
    // راهنمای ساده کپی پیست آیدی تلگرام در پنل بدون نیاز به کدهای پیچیده
    await tgSend(
      env,
      chatId,
      `👋 سلام! به ربات «دکتر خسته» خوش آمدید.\n\n⚠️ حساب تلگرام شما هنوز به هیچ ادمینی متصل نیست.\n\n<b>آیدی عددی تلگرام شما:</b>\n<code>${chatId}</code>\n\nلطفاً این آیدی عددی را کپی کرده و در پنل وب داشبورد (منوی ادمین‌ها -> ویرایش ادمین شما -> آیدی عددی تلگرام) وارد و ذخیره کنید تا ربات فوراً حساب شما را فعال کند!`
    );
    return new Response('ok');
  }

  return new Response('ok');
}
