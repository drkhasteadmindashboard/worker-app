// مدیریت داینامیک تنظیمات از دیتابیس با فال‌بک به متغیرهای محیطی wrangler.toml

export async function getSetting(env, key, defaultValue = '') {
  try {
    const row = await env.DB.prepare('SELECT value FROM settings WHERE key = ?').bind(key).first();
    if (row && row.value !== undefined && row.value !== null) return row.value;
  } catch (e) {
    // در صورتی که جدول ساخته نشده باشد، خودکار ساخته می‌شود
    try {
      await env.DB.prepare('CREATE TABLE IF NOT EXISTS settings (key TEXT PRIMARY KEY, value TEXT NOT NULL)').run();
    } catch (err) {}
  }
  return env[key] !== undefined ? String(env[key]) : defaultValue;
}

export async function setSetting(env, key, value) {
  try {
    await env.DB.prepare('INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value')
      .bind(key, String(value))
      .run();
  } catch (e) {
    try {
      await env.DB.prepare('CREATE TABLE IF NOT EXISTS settings (key TEXT PRIMARY KEY, value TEXT NOT NULL)').run();
      await env.DB.prepare('INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value')
        .bind(key, String(value))
        .run();
    } catch (err) {}
  }
}
