# دکتر خسته 🩺 — داشبورد دو نفره روی Cloudflare Workers (رایگان)

داشبورد مدیریت تسک و یادداشت برای دو ادمین، با ادغام کامل تلگرام (یادآوری خودکار موعد تسک‌ها + ربات) و بکاپ/هاست فایل روی گیت‌هاب. تمام سرویس‌های استفاده‌شده (Workers، D1، Cron Triggers) در پلن رایگان Cloudflare جا می‌شوند.

## امکانات
- ورود چند ادمین، با یک **ادمین اصلی** که تنها او می‌تواند ادمین جدید بسازد یا حذف کند.
- تسک با عنوان، توضیح، موعد، اولویت، واگذاری به ادمین، و وضعیت (در انتظار/در حال انجام/انجام‌شده).
- **تقویم** ماهانه با نقطه‌های رنگی روی روزهای دارای موعد.
- **یادداشت‌ها** با رنگ دلخواه هر ادمین و نمایش نام + رنگ اختصاصی نویسنده روی هر کارت.
- **ربات تلگرام**: اتصال حساب با کد یک‌بارمصرف، دستور `/tasks` برای دیدن تسک‌های باز، و ارسال خودکار یادآوری یک روز قبل، روز موعد، و هشدار تسک عقب‌افتاده (هر ۳۰ دقیقه بررسی می‌شود).
- **بکاپ خودکار روزانه به گیت‌هاب** (ساعت ۰۲:۰۰ UTC) + دکمه بکاپ دستی، و همچنین امکان آپلود فایل که مستقیم در ریپازیتوری گیت‌هاب ذخیره و از طریق لینک خام (raw) قابل نمایش است.
- طراحی مدرن، تیره، راست‌به‌چپ و کاملاً فارسی.

## پیش‌نیازها
- Node.js نسخه ۱۸ به بالا
- یک حساب رایگان Cloudflare
- یک ریپازیتوری گیت‌هاب (خصوصی یا عمومی) برای بکاپ/فایل‌ها
- یک ربات تلگرام ساخته‌شده با [@BotFather](https://t.me/BotFather) (توکن ربات را نگه دارید)

## مرحله ۱: نصب و ورود به Cloudflare
```bash
cd doctor-khaste
npm install
npx wrangler login

```

## مرحله ۲: ساخت دیتابیس D1 (رایگان)
```bash
npx wrangler d1 create doctor_khaste_db
```
خروجی این دستور یک `database_id` می‌دهد؛ آن را داخل `wrangler.toml` در قسمت `[[d1_databases]]` جایگزین `PUT-YOUR-D1-DATABASE-ID-HERE` کنید.

سپس ساختار جداول را روی دیتابیس واقعی اجرا کنید:
```bash
npm run db:init
```

## مرحله ۳: تنظیم مقادیر محرمانه
```bash
npx wrangler secret put TELEGRAM_BOT_TOKEN
npx wrangler secret put TELEGRAM_WEBHOOK_SECRET   # یک رشته تصادفی دلخواه بسازید، مثلاً با: openssl rand -hex 16
npx wrangler secret put GITHUB_TOKEN              # یک Personal Access Token با دسترسی contents:write روی همان ریپو
```
npx wrangler secret put 8713558836:AAFnRGsXBzVDOUQELtbQAeB9TRD_i-MUTXE
npx wrangler secret put 98h3yWW   
npx wrangler secret put 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     
## مرحله ۴: تنظیم مقادیر عمومی
در `wrangler.toml` بخش `[vars]` را ویرایش کنید:
```toml
GITHUB_REPO = "username/repo-name"
GITHUB_BRANCH = "main"
```

## مرحله ۵: دیپلوی
```bash
npm run deploy
```
آدرس نهایی چیزی شبیه `https://doctor-khaste-dashboard.YOUR-SUBDOMAIN.workers.dev` خواهد بود.

## مرحله ۶: اتصال وبهوک تلگرام
با مقدار `TELEGRAM_WEBHOOK_SECRET` که ساختید و آدرس Worker خود، این لینک را یک‌بار در مرورگر باز کنید (یا با curl بزنید):
```
https://api.telegram.org/bot<TELEGRAM_BOT_TOKEN>/setWebhook?url=https://YOUR-WORKER-URL/telegram/webhook/<TELEGRAM_WEBHOOK_SECRET>
```

## مرحله ۷: راه‌اندازی اولیه داشبورد
1. آدرس Worker را در مرورگر باز کنید — چون هنوز هیچ ادمینی وجود ندارد، صفحه‌ی «راه‌اندازی اولیه» نشان داده می‌شود.
2. نام، نام کاربری و رمز عبور خودتان را وارد کنید — این حساب به‌صورت خودکار **ادمین اصلی** می‌شود.
3. وارد داشبورد شوید، از منوی «ادمین‌ها» ادمین دوم (همکارتان) را بسازید.
4. هرکدام از ادمین‌ها از «تنظیمات → اتصال تلگرام» یک کد بگیرند و در چت ربات به‌صورت `/start CODE` ارسال کنند تا یادآوری‌ها برایشان فعال شود.

## نکات مهم
- **پلن رایگان کافی است**: Workers رایگان (۱۰۰ هزار درخواست در روز)، D1 رایگان (۵ گیگابایت و ۵ میلیون خواندن در روز)، و Cron Triggers هم در پلن رایگان مجاز است.
- توکن گیت‌هاب را با کمترین دسترسی ممکن (فقط `contents` روی همان یک ریپو، به‌صورت Fine-grained PAT) بسازید.
- کوکی نشست با `Secure` تنظیم شده و فقط روی HTTPS کار می‌کند (دامنه‌ی `workers.dev` به‌صورت پیش‌فرض HTTPS است).
- برای اجرای محلی (تست قبل از دیپلوی): `npm run dev` و `npm run db:init:local`.
- اگر خواستید دامنه اختصاصی خودتان را وصل کنید، از بخش Custom Domains در داشبورد Cloudflare Workers استفاده کنید (رایگان است).

## ساختار پروژه
```
doctor-khaste/
├── wrangler.toml       # تنظیمات Worker، D1، کرون
├── schema.sql           # ساختار جداول D1
├── package.json
├── src/
│   ├── index.js          # روتینگ API + کرون یادآوری/بکاپ
│   ├── auth.js           # هش رمز عبور، نشست، کوکی
│   ├── telegram.js        # ارسال پیام + وبهوک ربات
│   └── github.js          # بکاپ و آپلود فایل به گیت‌هاب
└── public/
    └── index.html         # کل داشبورد (SPA تک‌فایلی)
```
