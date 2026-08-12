/**
 * راهنمای راه‌اندازی و دیپلوی خودکار پروژه دکتر خسته روی کلودفلر ورکرز
 * این اسکریپت تمام مراحل ساخت دیتابیس، جایگذاری آیدی، ساخت ساختار جداول، تنظیم سکرت‌ها و دیپلوی نهایی را بر عهده دارد.
 */

const { execSync, spawn } = require('child_process');
const fs = require('fs');
const readline = require('readline');

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

const question = (query) => new Promise((resolve) => rl.question(query, resolve));

function runCommand(command) {
  console.log(`\x1b[36mRunning: ${command}\x1b[0m`);
  try {
    return execSync(command, { stdio: 'inherit' });
  } catch (error) {
    console.error(`\x1b[31mError running command: ${command}\x1b[0m`);
    process.exit(1);
  }
}

async function main() {
  console.clear();
  console.log('\x1b[35m================================================================');
  console.log('🩺 به اسکریپت راه‌اندازی و دیپلوی خودکار دکتر خسته خوش آمدید! 🩺');
  console.log('================================================================\x1b[0m\n');

  // مرحله ۱: ورود به کلودفلر
  console.log('\x1b[33mمرحله ۱: ورود به حساب کلودفلر شما...\x1b[0m');
  runCommand('npx wrangler login');

  // مرحله ۲: ساخت دیتابیس D1
  console.log('\n\x1b[33mمرحله ۲: ساخت دیتابیس D1...\x1b[0m');
  let databaseId = '';
  try {
    const output = execSync('npx wrangler d1 create doctor_khaste_db').toString();
    console.log(output);
    // تلاش برای استخراج ID از خروجی
    const idMatch = output.match(/database_id\s*=\s*"([^"]+)"/i) || output.match(/database_id\s*:\s*([^\s]+)/i) || output.match(/id\s*:\s*([a-f0-9-]+)/i) || output.match(/"database_id":\s*"([^"]+)"/);
    if (idMatch && idMatch[1]) {
      databaseId = idMatch[1];
    } else {
      // اگر ریپازیتوری قبلاً دیتابیس داشته باشد یا خطا بدهد، نام دیتابیس را به عنوان جایگزین بپرسیم یا تلاش کنیم لیست کنیم
      try {
        const listOutput = execSync('npx wrangler d1 list --json').toString();
        const dbs = JSON.parse(listOutput);
        const myDb = dbs.find(d => d.name === 'doctor_khaste_db');
        if (myDb) {
          databaseId = myDb.uuid || myDb.id;
        }
      } catch (e) {}
    }
  } catch (error) {
    console.log('\x1b[31mدیتابیس احتمالاً قبلاً ساخته شده است. تلاش برای دریافت آیدی...\x1b[0m');
    try {
      const listOutput = execSync('npx wrangler d1 list --json').toString();
      const dbs = JSON.parse(listOutput);
      const myDb = dbs.find(d => d.name === 'doctor_khaste_db');
      if (myDb) {
        databaseId = myDb.uuid || myDb.id;
      }
    } catch (e) {}
  }

  if (!databaseId) {
    console.log('\x1b[31mنتوانستیم شناسه دیتابیس را به طور خودکار پیدا کنیم.\x1b[0m');
    databaseId = await question('\x1b[32mلطفاً شناسه دیتابیس D1 را دستی وارد کنید: \x1b[0m');
    databaseId = databaseId.trim();
  }

  console.log(`\x1b[32mشناسه دیتابیس یافت شد: ${databaseId}\x1b[0m`);

  // مرحله ۳: به روز رسانی wrangler.toml
  console.log('\n\x1b[33mمرحله ۳: بروزرسانی فایل wrangler.toml...\x1b[0m');
  if (fs.existsSync('wrangler.toml')) {
    let content = fs.readFileSync('wrangler.toml', 'utf8');
    // جایگزینی شناسه دیتابیس قبلی با شناسه جدید
    content = content.replace(/database_id\s*=\s*"[^"]*"/g, `database_id = "${databaseId}"`);
    fs.writeFileSync('wrangler.toml', content, 'utf8');
    console.log('\x1b[32mفایل wrangler.toml با موفقیت بروزرسانی شد.\x1b[0m');
  } else {
    console.log('\x1b[31mخطا: فایل wrangler.toml یافت نشد!\x1b[0m');
    process.exit(1);
  }

  // مرحله ۴: مقداردهی اولیه جداول دیتابیس کلودفلر
  console.log('\n\x1b[33mمرحله ۴: مقداردهی ساختار جداول روی دیتابیس ابری...\x1b[0m');
  runCommand('npm run db:init');

  // مرحله ۵: تنظیم سکرت‌ها و مقادیر محرمانه به صورت خودکار
  console.log('\n\x1b[33mمرحله ۵: تنظیم مقادیر محرمانه و سکرت‌ها...\x1b[0m');
  console.log('در این مرحله مقادیر مورد نیاز برای بات تلگرام و گیت‌هاب را وارد کنید.');

  const botToken = await question('\x1b[32mلطفاً توکن ربات تلگرام (TELEGRAM_BOT_TOKEN) را وارد کنید: \x1b[0m');
  if (botToken.trim()) {
    const cmd = spawn('npx', ['wrangler', 'secret', 'put', 'TELEGRAM_BOT_TOKEN'], { stdio: ['pipe', 'inherit', 'inherit'] });
    cmd.stdin.write(botToken.trim() + '\n');
    cmd.stdin.end();
    await new Promise(resolve => cmd.on('close', resolve));
  }

  const webhookSecret = await question('\x1b[32mلطفاً یک رمز تصادفی برای وبهوک تلگرام (TELEGRAM_WEBHOOK_SECRET) وارد کنید (یا اینتر بزنید تا خودکار تولید شود): \x1b[0m');
  let finalWebhookSecret = webhookSecret.trim();
  if (!finalWebhookSecret) {
    finalWebhookSecret = require('crypto').randomBytes(16).toString('hex');
    console.log(`\x1b[36mرمز وبهوک تولید شده: ${finalWebhookSecret}\x1b[0m`);
  }
  const cmdWeb = spawn('npx', ['wrangler', 'secret', 'put', 'TELEGRAM_WEBHOOK_SECRET'], { stdio: ['pipe', 'inherit', 'inherit'] });
  cmdWeb.stdin.write(finalWebhookSecret + '\n');
  cmdWeb.stdin.end();
  await new Promise(resolve => cmdWeb.on('close', resolve));

  const githubToken = await question('\x1b[32mلطفاً توکن شخصی گیت‌هاب (GITHUB_TOKEN) را وارد کنید (جهت بکاپ خودکار): \x1b[0m');
  if (githubToken.trim()) {
    const cmdGit = spawn('npx', ['wrangler', 'secret', 'put', 'GITHUB_TOKEN'], { stdio: ['pipe', 'inherit', 'inherit'] });
    cmdGit.stdin.write(githubToken.trim() + '\n');
    cmdGit.stdin.end();
    await new Promise(resolve => cmdGit.on('close', resolve));
  }

  // مرحله ۶: دیپلوی نهایی
  console.log('\n\x1b[33mمرحله ۶: دیپلوی نهایی پروژه دکتر خسته به Cloudflare Workers...\x1b[0m');
  runCommand('npm run deploy');

  console.log('\n\x1b[32m================================================================');
  console.log('🎉 پروژه شما با موفقیت نصب، پیکربندی و دیپلوی شد! 🎉');
  console.log('================================================================\x1b[0m');
  console.log(`\x1b[36mرمز وبهوک تلگرام شما: ${finalWebhookSecret}`);
  console.log('حالا می‌توانید طبق راهنمای گام‌به‌گام در README.md، وبهوک تلگرام خود را فعال کنید.');
  console.log('ممنون از اینکه از دکتر خسته استفاده می‌کنید! 🩺\x1b[0m\n');

  rl.close();
}

main().catch(err => {
  console.error(err);
  rl.close();
  process.exit(1);
});
