<div align="center" dir="rtl">

# ⏳ DeadlineFlow

**سیستم مدیریت پروژه، تسک و تایم لاین متن باز (Open Source)**

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![React](https://img.shields.io/badge/React-19-61DAFB?style=for-the-badge&logo=react)](https://react.dev)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker)](https://docker.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

یک ابزار سبک، سریع و اپن سورس برای مدیریت ددلاین ها و تایم لاین پروژه ها.

[Live Demo](http://deadlineflow.ir) • 
[مشاهده مستندات API](http://localhost:8080/api/documentation) • 
[گزارش باگ](https://github.com/MortezaZareiZakliki/deadlineflow/issues) • 
[درخواست فیچر](https://github.com/MortezaZareiZakliki/deadlineflow/issues)

</div>

---

## 📖 درباره پروژه

**DeadlineFlow** یک سیستم مدیریت پروژه ی ساده و در عین حال قدرتمند است که با تمرکز بر روی **ددلاین ها** و **تایم لاین ها** طراحی شده است. برخلاف ابزارهای سنگین مثل جیرا (Jirra) که برای تیم های بزرگ ساخته شده اند، **ددلاین فلو** برای تیم های کوچک، فریلنسرها و استارتاپ ها ساخته شده تا بدون پیچیدگی، پروژه هایشان را مدیریت کنند.

> برای خواندن داستان کامل پروژه، محدودیت ها و نقشه راه، فایل [PROJECT_STORY.md](PROJECT_STORY.md) را مطالعه کنید.

---

## ✨ امکانات (Features)

### نسخه ۱.۰.۰ (MVP - فعلی)
- [x] احراز هویت با Laravel Sanctum
- [x] مدیریت پروژه ها (CRUD)
- [x] مدیریت نسخه ها (Versions) با مرتب سازی SemVer
- [x] مدیریت تسک ها با ددلاین و اولویت
- [x] گروه بندی تسک ها بر اساس پروژه
- [x] داشبورد داینامیک (آمار + نمودار + تسک های نزدیک)
- [x] تقویم شمسی (Jalali) با `react-multi-date-picker`
- [x] مدیریت پروفایل (تغییر نام و رمز عبور)
- [x] مدیریت نشست ها (IP, User-Agent, Logout از راه دور)
- [x] اعلان های داخلی (In-App Notifications)
- [x] Audit Logging (ردیابی تغییرات)
- [x] یادآوری خودکار ددلاین ها (Scheduler + Command)
- [x] صف بندی ایمیل ها و نوتیفیکیشن ها با Redis و RabbitMQ
- [x] مانیتورینگ صف ها با Laravel Horizon
- [x] کش کردن داده ها با Redis
- [x] مستندات API با Swagger
- [x] امنیت چندلایه (XSS, CSRF, Rate Limiting, Password Policy)
- [x] Docker & Docker Compose
- [x] Nginx (برای پروداکشن)
- [x] تست های خودکار (Feature & Unit Tests)

### فیچرهای آینده (Roadmap)
- [ ] نمودار گانت (Gantt Chart) برای تایم لاین
- [ ] سیستم ایده‌ها و یادداشت‌ها (Ideas)
- [ ] پشتیبانی از چند تیم (Multi-tenancy)
- [ ] و هزاران ...

برای مشاهده ی نقشه راه کامل، فایل [ROADMAP.md](ROADMAP.md) را ببینید.

---

## 🛠 تکنولوژی ها (Tech Stack)

### Back-End
- **PHP 8.5** - زبان برنامه نویسی
- **Laravel 13** - فریم ورک اصلی
- **Laravel Sanctum** - احراز هویت API
- **MySQL 8.4** - دیتابیس اصلی
- **Redis** - کش و صف بندی (Queue)
- **RabbitMQ** - پیام رسانی پیشرفته
- **Laravel Horizon** - مانیتورینگ صف های Redis
- **Nginx** - وب سرور (پروداکشن)

### Front-End
- **React 19** - کتابخانه UI
- **Vite** - Build Tool
- **TailwindCSS** - استایل دهی
- **Framer Motion** - انیمیشن
- **Recharts** - نمودار
- **react-multi-date-picker** - تقویم شمسی

### DevOps & Security
- **Docker & Docker Compose** - (Containerize) کانتینرایز
- **Docker Secrets** - مدیریت امن رمزها در پروداکشن
- **Non-root User** - اجرای امن کانتینرها
- **WSL2 (Ubuntu)** - محیط توسعه

---

## 🚀 نصب و راه اندازی (Quick Start)

برای راهنمای کامل نصب (از صفر تا صد، شامل نصب WSL و Docker)، فایل [INSTALLATION.md](INSTALLATION.md) را مطالعه کنید.

### خلاصه سریع

    git clone https://github.com/MortezaZareiZakliki/deadlineflow.git
    cd deadlineflow
    cp src/.env.example src/.env
    docker-compose up -d --build
    docker-compose exec app composer install
    docker-compose exec app php artisan key:generate
    docker-compose exec app php artisan migrate:fresh --seed
    docker-compose exec app npm install && docker-compose exec app npm run build

### دسترسی‌ها

پس از بالا آمدن پروژه، سرویس های مختلف از طریق آدرس های زیر در دسترس هستند.

**نکته مهم برای دسترسی به API:** اگر از ویندوز به پروژه متصل می‌شوید (WSL2)، باید به جای `localhost` از **IP داخلی WSL** استفاده کنید. برای پیدا کردن این IP، یکی از روش های زیر را امتحان کنید:
1. **روش اول (از داخل WSL):** در ترمینال Ubuntu دستور `hostname -I` را اجرا کنید.
2. **روش دوم (از ویندوز):** در CMD یا PowerShell ویندوز دستور `ipconfig` را اجرا کنید و به دنبال آدرس IPv4 مربوط به **vEthernet (WSL)** بگردید.
3. **روش سوم (آدرس پیش فرض):** در بسیاری از تنظیمات WSL2، آدرس `http://localhost:8080` نیز به درستی کار می کند.

آدرسی که دریافت می‌کنید را جایگزین `YOUR_WSL_IP` کنید.

| سرویس | آدرس |
|-------|------|
| API | `http://YOUR_WSL_IP:8080` |
| مستندات Swagger | `http://YOUR_WSL_IP:8080/api/documentation` |
| پنل مدیریت RabbitMQ | `http://YOUR_WSL_IP:15672` (نام کاربری: `admin`، رمز عبور: `rabbitmq`) |
| داشبورد Horizon | `http://YOUR_WSL_IP:8080/horizon` (نام کاربری: `admin`، رمز عبور: horizon) |

> **راهنمای سریع:** اگر IP داخلی WSL را نمی دانید، در ترمینال Ubuntu دستور `hostname -I` را بزنید. همچنین در محیط لوکال، می‌توانید آدرس `http://localhost:8080` را نیز امتحان کنید (بسته به تنظیمات شبکه شما).

### کاربر تستی (Demo User)

    Email:    demo@deadlineflow.ir
    Password: password

---

## 🚢 استقرار در پروداکشن (Production Deployment)

این پروژه برای استقرار در محیط پروداکشن (VPS) بهینه سازی شده است. زیرساخت لازم برای اجرای امن و مقیاس پذیر در محیط واقعی آماده است.

### پیش نیازها

- یک سرور VPS (Ubuntu 26.04.1 LTS یا بالاتر)
- Docker & Docker Compose
- دامنه (اختیاری، برای HTTPS)

### مراحل استقرار

#### ۱. آماده سازی Secrets

رمزهای حساس در فایل های جداگانه ذخیره می شوند تا هرگز در گیت هاب قرار نگیرند:

    mkdir -p secrets

    echo "base64:YOUR_APP_KEY" > secrets/app_key.txt
    echo "root" > secrets/mysql_root_password.txt
    echo "root" > secrets/mysql_password.txt
    echo "redis" > secrets/redis_password.txt
    echo "rabbitmq" > secrets/rabbitmq_password.txt
    echo "horizon" > secrets/horizon_password.txt

> ⚠️ **نکته:** پوشه ی `secrets/` در `.gitignore` قرار دارد و هرگز به گیت هاب ارسال نمی شود.

#### ۲. تنظیم `.env` برای پروداکشن

در محیط پروداکشن، رمزها از `.env` حذف می شوند و `entrypoint.sh` آن ها را از `secrets/` می خواند:

    APP_ENV=production
    APP_DEBUG=false
    # DB_PASSWORD= (خالی)
    # REDIS_PASSWORD= (خالی)
    # RABBITMQ_PASSWORD= (خالی)
    # HORIZON_PASSWORD= (خالی)

#### ۳. اجرای پروژه

    docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

#### ۴. بررسی وضعیت

    docker-compose ps
    docker-compose exec app php artisan migrate --force
    docker-compose exec app php artisan optimize

### ساختار امنیتی پروداکشن

| لایه | توضیح |
|------|-------|
| **Docker Secrets** | رمزها در فایل های جداگانه (خارج از گیت) ذخیره می شوند. |
| **کاربر Non-Root** | کانتینرها با کاربر `appuser` (نه root) اجرا می شوند. |
| **Nginx** | فایل های مخفی (`.env`, `.git`) بلاک می شوند. |
| **HTTPS** | در پروداکشن، `URL::forceScheme('https')` فعال می شود. |
| **Rate Limiting** | از Brute Force جلوگیری می کند. |
| **Audit Logging** | هر تغییری در دیتابیس ثبت می شود. |
| **Horizon** | با Basic Auth (از Secrets) محافظت می شود. |

---

## 🏗 معماری پروژه (Architecture)

این پروژه بر اساس **Clean Architecture** و **Design Patterns** زیر ساخته شده است:

    src/
    ├── app/
    │   ├── Console/Commands/    # Artisan Commandها
    │   ├── Http/
    │   │   ├── Controllers/Api/ # کنترلرهای API
    │   │   ├── Requests/        # اعتبارسنجی ورودی ها
    │   │   └── Resources/       # فرمت بندی خروجی JSON
    │   ├── Jobs/                # Jobهای پس زمینه
    │   ├── Models/              # مدل های Eloquent
    │   ├── Notifications/       # اعلان ها
    │   ├── OpenApi/             # کانفیگ Swagger
    │   ├── Repositories/
    │   │   ├── Contracts/       # اینترفیس های Repository
    │   │   └── Eloquent/        # پیاده سازی Repository
    │   ├── Services/            # منطق تجاری
    │   └── Traits/              # Trait های مشترک
    ├── database/
    │   ├── migrations/          # مایگریشن ها
    │   └── seeders/             # دیتای نمونه
    ├── resources/
    │   ├── css/app.css          # استایل ها (Tailwind)
    │   ├── js/                  # کدهای React
    │   │   ├── api/             # تنظیمات Axios
    │   │   ├── components/      # کامپوننت ها
    │   │   ├── context/         # Context (Auth)
    │   │   ├── pages/           # صفحات
    │   │   ├── types/           # Typeها
    │   │   └── utils/           # Helperها
    │   └── views/app.blade.php  # نقطه ورود
    └── routes/
        ├── api.php              # روت های API
        └── console.php          # زمان بندی Commandها

### الگوهای استفاده شده

- ✅ **Repository Pattern** - جداسازی منطق دیتابیس
- ✅ **Service Layer** - جداسازی منطق تجاری
- ✅ **Form Request** - اعتبارسنجی متمرکز
- ✅ **API Resource** - فرمت بندی خروجی
- ✅ **Queue Jobs** - پردازش پس زمینه
- ✅ **Trait** - کدهای مشترک (ApiResponse, SanitizesInput)
- ✅ **Audit Logging** - ردیابی تغییرات

---

## 📚 مستندات API

پس از راه اندازی، مستندات کامل API در آدرس زیر در دسترس است:

**http://YOUR_WSL_IP:8080/api/documentation**

---

## 🧪 تست ها

    # اجرای همه تست ها
    docker-compose exec app php artisan test

    # فقط تست های Feature
    docker-compose exec app php artisan test --testsuite=Feature

    # فقط تست های Unit
    docker-compose exec app php artisan test --testsuite=Unit

---

## 🤝 مشارکت (Contributing)

از هرگونه مشارکت استقبال می کنیم! لطفاً قبل از ارسال Pull Request، فایل [CONTRIBUTING.md](CONTRIBUTING.md) را مطالعه کنید.

---

## 📄 لایسنس (License)

این پروژه تحت لایسنس **MIT** منتشر شده است. برای اطلاعات بیشتر فایل [LICENSE](LICENSE) را ببینید.

---

<div align="center" dir="rtl">

**ساخته شده با ❤️ توسط [مرتضی زارعی زکلیکی](https://github.com/MortezaZareiZakliki)**

⭐ اگر این پروژه را دوست داشتید، به آن ستاره بدهید! ⭐

</div>