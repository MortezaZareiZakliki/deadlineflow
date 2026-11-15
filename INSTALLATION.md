<div align="center" dir="rtl">

# 🚀 راهنمای کامل نصب و راه اندازی DeadlineFlow

این راهنما، پروژه را **از صفر تا صد** روی ویندوز راه اندازی می کند. حتی اگر هیچ چیز نصب نداری، با دنبال کردن این مراحل، پروژه به درستی بالا می آید.

</div>

---

## 📋 پیش نیازها

- **سیستم عامل:** ویندوز ۱۰ یا ویندوز ۱۱
- **رم:** حداقل ۸ گیگابایت (پیشنهادی: ۱۶ گیگابایت)
- **فضای دیسک:** حداقل ۱۰ گیگابایت آزاد

---

## 🎯 مرحله ۱: نصب WSL2 (Windows Subsystem for Linux)

### WSL چیه؟
WSL یک ویژگی ویندوزه که به تو اجازه می ده یک لینوکس کامل (مثل Ubuntu) رو **داخل ویندوز** اجرا کنی، بدون نیاز به نصب مجدد سیستم عامل یا ساخت ماشین مجازی سنگین.

### چرا بهش نیاز داریم؟
چون Docker روی ویندوز، از WSL2 برای اجرای کانتینرهای لینوکسی استفاده می کند. بدون WSL2، Docker Desktop کار نمی کند.

### ۱.۱. فعال سازی WSL با PowerShell

۱. کلید **Windows** را بزنید.

۲. تایپ کنید: **PowerShell**

۳. روی **Windows PowerShell** راست کلیک کنید.

۴. **Run as administrator** را انتخاب کنید.

۵. **Yes** بزنید (اگر پرسید).

### ۱.۲. اجرای دستور نصب WSL

در PowerShell (که ادمین است)، دستور زیر را بزنید:

    wsl --install

**⏳ صبر کنید.** این دستور:
- WSL را فعال می کند.
- Virtual Machine Platform را فعال می کند.
- Ubuntu را دانلود و نصب می کند.

نکته مهم: اگر بعد از اتمام دستور، ری استارت کردید و دیدید که Ubuntu نصب نشده یا باز نشد، می توانید با دستور زیر آن را به صورت دستی اجرا کنید:

    wsl -d Ubuntu


### ۱.۳. ری استارت کامپیوتر

**⚠️ این مرحله حیاتی است!** پس از اتمام دستور، کامپیوتر خود را **ری ‌استارت** کنید.

### ۱.۴. تنظیم Ubuntu (بعد از ری استارت)

بعد از ری استارت، ترمینال Ubuntu **خودکار** باز می شود و از شما می خواهد:

    Enter new UNIX username:

- **یک نام کاربری وارد کنید** (مثلاً `morteza`).
- **یک رمز عبور قوی بگذارید** (حداقل ۸ کاراکتر).

**⚠️ نکته:** موقع تایپ رمز، هیچ چیزی نشان داده نمی شود (نه ستاره، نه نقطه). این عادی است.

### ۱.۵. بررسی نصب WSL

در PowerShell بزنید:

    wsl --status

**باید ببینید:**
    
    Default Version: 2

---

## 🎯 مرحله ۲: نصب Docker Desktop

### Docker چیه؟
Docker یک ابزار برای **کانتینرایز کردن** برنامه هاست. هر سرویس (PHP, MySQL, Redis, ...) در یک کانتینر جداگانه اجرا می شود.

### چرا از Docker استفاده می کنیم؟
- **محیط یکسان:** پروژه روی هر سیستمی (ویندوز، مک، لینوکس) یکسان اجرا می شود.
- **نصب آسان:** نیازی به نصب دستی PHP, MySQL, Redis نیست.
- **ایزوله:** هر سرویس در کانتینر خودش است، تداخلی ندارد.

### ۲.۱. دانلود Docker Desktop

۱. به سایت [docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop/) بروید.

۲. **Download for Windows** را بزنید.

۳. فایل `Docker Desktop Installer.exe` را دانلود کنید.

### ۲.۲. نصب Docker Desktop

۱. فایل Installer را اجرا کنید.

۲. **"Use WSL 2 instead of Hyper-V"** را تیک بزنید.

۳. **OK** بزنید.

۴. **⏳ صبر کنید** تا نصب تمام شود.

۵. **Close and restart** بزنید.

### ۲.۳. راه اندازی Docker Desktop

۱. بعد از ری استارت، Docker Desktop را باز کنید.

۲. **Accept** را بزنید (قوانین).

۳. **Skip tutorial** را بزنید.

۴. **⏳ صبر کنید** تا آیکون نهنگ (🐳) در سینی سیستم سبز شود و بنویسد **"Engine running"**.

### ۲.۴. بررسی نصب Docker

در PowerShell بزنید:

    docker --version
    docker-compose --version

**باید ببینید:**

    Docker version 27.x.x
    Docker Compose version v2.x.x

---

## 🎯 مرحله ۳: تنظیم WSL (بهینه سازی)

### ۳.۱. ساخت فایل `.wslconfig`

این فایل، منابع WSL (RAM, CPU) را محدود می کند تا ویندوز شما کند نشود.

**در PowerShell بزنید:**

    notepad "$env:USERPROFILE\.wslconfig"

**اگر پرسید «فایل را بسازم؟»، Yes بزنید.**

**این محتوا را در آن کپی کنید:**

    [wsl2]
    memory=8GB
    processors=4
    swap=2GB
    localhostForwarding=true

    [experimental]
    autoMemoryReclaim=gradual
    sparseVhd=true

**⚠️ نکته:** 
- اگر RAM سیستم ۸ گیگ است، `memory=4GB` بگذارید.
- اگر RAM سیستم ۱۶ گیگ است، `memory=8GB` بگذارید.

### ۳.۲. ری استارت WSL

در PowerShell بزنید:

    wsl --shutdown

**⏳ صبر کنید ۸ ثانیه.**

---

## 🎯 مرحله ۴: کلون کردن پروژه

### ۴.۱. باز کردن ترمینال Ubuntu

از منوی Start، تایپ کنید **Ubuntu** و روی آن کلیک کنید.

### ۴.۲. رفتن به پوشه ی پروژه

    cd ~
    mkdir -p projects
    cd projects

### ۴.۳. کلون کردن پروژه

    git clone https://github.com/MortezaZareiZakliki/deadlineflow.git
    cd deadlineflow

### ۴.۴. بررسی فایل ها

    ls -la

**باید ببینید:**

    docker-compose.yml
    Dockerfile
    src/
    docker/
    README.md
    ...

---

## 🎯 مرحله ۵: راه اندازی پروژه

### ۵.۱. کپی کردن فایل محیطی

    cp src/.env.example src/.env

### ۵.۲. ساخت و اجرای کانتینرها

    docker-compose up -d --build

**⏳ صبر کنید.** ممکن است ۵ تا ۱۰ دقیقه طول بکشد (چون دارد ایمیج ها را دانلود می کند).

### ۵.۳. بررسی وضعیت کانتینرها

    docker-compose ps

**باید ببینید:**

    deadlineflow_app          Up
    deadlineflow_db           Up (healthy)
    deadlineflow_redis        Up
    deadlineflow_rabbitmq     Up
    deadlineflow_nginx        Up

**⚠️ اگر یکی از سرویس ها `Restarting` بود، صبر کنید تا `Up` شود.**

### ۵.۴. نصب پکیج های Composer

    docker-compose exec app composer install

### ۵.۵. تولید کلید اپلیکیشن

    docker-compose exec app php artisan key:generate

### ۵.۶. اجرای مایگریشن ها و دیتای نمونه

    docker-compose exec app php artisan migrate:fresh --seed

**باید ببینید:**

    INFO  Running migrations.
    ...
    INFO  Seeding database.

### ۵.۷. نصب پکیج های Node و Build

    docker-compose exec app npm install
    docker-compose exec app npm run build

### ۵.۸. ساخت مستندات Swagger

    docker-compose exec app php artisan l5-swagger:generate

---

## 🎯 مرحله ۶: دسترسی به پروژه

### ۶.۱. پیدا کردن IP

برای دسترسی به پروژه از مرورگر ویندوز، باید آدرس IP ماشین لینوکسی (WSL) را پیدا کنید. بسته به محیطی که استفاده می کنید، یکی از روش های زیر را انتخاب کنید:

**روش اول (در ویندوز - WSL2):**
اگر از ویندوز استفاده می‌کنید، در CMD یا PowerShell ویندوز دستور زیر را اجرا کنید:

    ipconfig

سپس به دنبال آدرسی بگردید که در بخش **vEthernet (WSL)** قرار دارد.

**روش دوم (در لینوکس واقعی یا WSL):**
در ترمینال Ubuntu دستور زیر را اجرا کنید:

    hostname -I

این دستور آدرس IP داخلی ماشین لینوکسی را نشان می‌دهد.

**روش سوم (ساده ترین حالت):**
در اکثر تنظیمات جدید WSL2، آدرس `localhost` (یا `http://localhost:8080`) نیز به درستی کار می کند و نیازی به پیدا کردن IP نیست.

**این IP را یادداشت کنید** و در مراحل بعدی (دسترسی به پروژه) از آن استفاده کنید.

### ۶.۲. باز کردن در مرورگر

در مرورگر ویندوز به آدرس زیر بروید:

    http://<IP>:8080

### ۶.۳. لاگین با کاربر تستی

    Email:    demo@deadlineflow.ir
    Password: password

---

## 🎯 مرحله ۷: اجرای Vite (برای توسعه)

**اگر می خواهید کدهای React را تغییر دهید و Hot Reload داشته باشید:**

در ترمینال Ubuntu:

    docker-compose exec app npm run dev

**Vite روی پورت `5173` اجرا می شود.**

---

## 🎯 مرحله ۸: دسترسی به سرویس های جانبی

| سرویس | آدرس |
|-------|------|
| **API** | `http://<IP>:8080` |
| **مستندات Swagger** | `http://<IP>:8080/api/documentation` |
| **پنل RabbitMQ** | `http://<IP>:15672` (admin/rabbitmq) |
| **داشبورد Horizon** | `http://<IP>:8080/horizon` (admin/horizon) |

---

## 🎯 مرحله ۹: دستورات مفید

### اجرای تست ها
    docker-compose exec app php artisan test

### پاک کردن کش
    docker-compose exec app php artisan cache:clear
    docker-compose exec app php artisan config:clear

### متوقف کردن پروژه
    docker-compose down

### پاک کردن کامل (شامل دیتابیس)
    docker-compose down -v

> ⚠️ **هشدار:** این دستور تمام کانتینرها و **دیتابیس** را به طور کامل پاک می کند. قبل از اجرای آن، حتماً از داده های خود بک آپ بگیرید.

### مشاهده لاگ ها
    docker-compose logs -f app
    docker-compose logs -f queue
    docker-compose logs -f scheduler

---

## ⚠️ مشکلات رایج و راه حل ها

### ۱. خطای `Port 8080 is already in use`
**راه حل:** یک برنامه دیگر از پورت ۸۰۸۰ استفاده می کند. در `docker-compose.yml`، پورت را به `8081:80` تغییر دهید.

### ۲. خطای `Permission denied` در `storage/logs`
**راه حل:**

    docker-compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

### ۳. خطای `Connection refused` به MySQL
**راه حل:** صبر کنید تا MySQL کاملاً بالا بیاید (Health Check). `docker-compose ps` را چک کنید.

### ۴. خطای `Class not found` در لاراول
**راه حل:**

    docker-compose exec app composer dump-autoload

### ۵. VS Code به WSL وصل نمی شود
**راه حل:**

    rm -rf ~/.vscode-server

بعد VS Code را ببندید و دوباره باز کنید.

### ۶. کندی دانلود یا خطای شبکه در زمان Build (تغییر Mirror)
در این پروژه، برای افزایش سرعت دانلود پکیج ها در ایران، از آینه (Mirror) داخلی `mirror.arvancloud.ir` در فایل `Dockerfile` استفاده شده است. اگر در خارج از ایران هستید یا آینه‌ی دیگری را ترجیح می دهید، می‌توانید فایل `Dockerfile` را باز کرده و خط زیر را پیدا کنید:

    RUN sed -i 's/deb.debian.org/mirror.arvancloud.ir/g' /etc/apt/sources.list.d/debian.sources || \
        sed -i 's/deb.debian.org/mirror.arvancloud.ir/g' /etc/apt/sources.list

سپس `mirror.arvancloud.ir` را با آینه‌ی دلخواه خود (مثلاً `deb.debian.org` برای سرورهای اصلی) جایگزین کنید.

---

## 🎉 تبریک!

پروژه ی DeadlineFlow با موفقیت روی سیستم شما راه اندازی شد. حالا می توانید:
- پروژه بسازید.
- برای پروژه نسخه تعریف کنید.
- تسک با ددلاین بسازید.
- داشبورد را ببینید.
- اعلان ها را دریافت کنید.

---

## 💬 پشتیبانی و ارتباط با توسعه دهنده

اگر در هر مرحله از نصب یا استفاده از DeadlineFlow به مشکلی برخوردید، یا سوالی درباره ی معماری و نحوه ی کارکرد پروژه داشتید، خوشحال می شوم کمکتان کنم.

- **ایجاد Issue:** برای گزارش باگ یا پیشنهاد فیچر، از بخش [Issues](https://github.com/MortezaZareiZakliki/deadlineflow/issues) استفاده کنید.
- **ارتباط مستقیم:** اگر نیاز به پاسخ سریع تر دارید، می توانید در [لینکدین](https://www.linkedin.com/in/morteza-zarei-zakliki/) به من پیام دهید.
  
من همیشه از دریافت بازخورد شما و مشارکت در توسعه ی این پروژه خوشحال می شوم. ❤️

---

**برای اطلاعات بیشتر، به [README.md](README.md) و [ROADMAP.md](ROADMAP.md) مراجعه کنید.**

---

<div align="center" dir="rtl">

**«اگر این راهنما به کارتان آمد، به پروژه ⭐ بدهید!»**

</div>