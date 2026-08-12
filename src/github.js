// ادغام گیت‌هاب: بکاپ خودکار و هاست فایل‌ها روی ریپازیتوری از طریق GitHub Contents API
import { getSetting } from './config.js';

export function toBase64Unicode(str) {
  return btoa(unescape(encodeURIComponent(str)));
}

// انکد امن مسیرها برای سازگاری کامل با کاراکترهای فارسی و فولدربندی ابسیدین در گیت‌هاب
function encodePath(path) {
  return path.split('/').map(segment => encodeURIComponent(segment)).join('/');
}

async function ghFetch(env, path, opts = {}) {
  const branch = await getSetting(env, 'GITHUB_BRANCH', 'main');
  const repo = await getSetting(env, 'GITHUB_REPO');
  const token = await getSetting(env, 'GITHUB_TOKEN');

  const url = `https://api.github.com/repos/${repo}/contents/${encodePath(path)}`;
  return fetch(url, {
    ...opts,
    headers: {
      Authorization: `token ${token}`, // استفاده از پیشوند استاندارد token برای سازگاری حداکثری با انواع توکن‌ها
      'User-Agent': 'doctor-khaste-worker',
      Accept: 'application/vnd.github+json',
      ...(opts.headers || {}),
    },
  });
}

async function getFileSha(env, path) {
  const branch = await getSetting(env, 'GITHUB_BRANCH', 'main');
  const res = await ghFetch(env, `${path}?ref=${branch}`);
  if (res.status === 200) {
    const j = await res.json();
    return j.sha;
  }
  return null;
}

// content: رشته base64 (برای فایل باینری) یا متن ساده (برای JSON که خودمان base64 می‌کنیم)
export async function putFile(env, path, contentBase64, message) {
  const repo = await getSetting(env, 'GITHUB_REPO');
  const token = await getSetting(env, 'GITHUB_TOKEN');
  const branch = await getSetting(env, 'GITHUB_BRANCH', 'main');

  if (!token || !repo) {
    throw new Error('تنظیمات گیت‌هاب کامل نیست (GITHUB_TOKEN یا GITHUB_REPO در بخش تنظیمات یا متغیرها وجود ندارد).');
  }
  const sha = await getFileSha(env, path);
  const body = {
    message,
    content: contentBase64,
    branch: branch,
  };
  if (sha) body.sha = sha;
  const res = await ghFetch(env, path, { method: 'PUT', body: JSON.stringify(body) });
  if (!res.ok) {
    const errText = await res.text();
    throw new Error(`خطای گیت‌هاب (${res.status}): ${errText}`);
  }
  return res.json();
}

export async function backupDatabaseToGithub(env) {
  const tables = ['admins', 'tasks', 'notes'];
  const dump = {};
  for (const t of tables) {
    const { results } = await env.DB.prepare(`SELECT * FROM ${t}`).all();
    if (t === 'admins') {
      // هرگز هش رمز عبور یا نمک را در بکاپ عمومی قرار نده
      dump[t] = results.map(({ password_hash, salt, telegram_link_code, ...rest }) => rest);
    } else {
      dump[t] = results;
    }
  }
  dump._backed_up_at = new Date().toISOString();

  const dir = await getSetting(env, 'GITHUB_BACKUP_DIR', 'backups');
  const stamp = new Date().toISOString().slice(0, 10);
  const path = `${dir}/backup-${stamp}.json`;
  const latestPath = `${dir}/latest.json`;
  const contentB64 = toBase64Unicode(JSON.stringify(dump, null, 2));

  await putFile(env, path, contentB64, `بکاپ خودکار ${stamp}`);
  await putFile(env, latestPath, contentB64, `به‌روزرسانی آخرین بکاپ (${stamp})`);
  return path;
}

// آپلود فایل ضمیمه (مثلا تصویر یک یادداشت/تسک) و بازگرداندن URL خام قابل نمایش
export async function uploadFileToGithub(env, filename, base64Content) {
  const dir = await getSetting(env, 'GITHUB_FILES_DIR', 'files');
  const safeName = `${Date.now()}-${filename.replace(/[^a-zA-Z0-9._-]/g, '_')}`;
  const path = `${dir}/${safeName}`;
  await putFile(env, path, base64Content, `آپلود فایل: ${safeName}`);
  const branch = await getSetting(env, 'GITHUB_BRANCH', 'main');
  const repo = await getSetting(env, 'GITHUB_REPO');
  const rawUrl = `https://raw.githubusercontent.com/${repo}/${branch}/${path}`;
  return { path, url: rawUrl };
}
