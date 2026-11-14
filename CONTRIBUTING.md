<div align="center" dir="rtl">

# 🤝 راهنمای مشارکت در DeadlineFlow

از اینکه می خواهید در توسعه ی DeadlineFlow مشارکت کنید، صمیمانه سپاسگزاریم! این راهنما به شما کمک می کند تا فرآیند مشارکت را به بهترین شکل طی کنید.

</div>

---

## 📋 پیش از شروع

### ۱. مطالعه مستندات
لطفاً قبل از هر چیز، فایل های زیر را مطالعه کنید:
- [README.md](README.md) - معرفی کلی پروژه
- [PROJECT_STORY.md](PROJECT_STORY.md) - داستان و فلسفه ی پروژه
- [ROADMAP.md](ROADMAP.md) - نقشه راه و فیچرهای آینده

### ۲. بررسی Issues موجود
قبل از شروع کار روی یک فیچر یا باگ، مطمئن شوید که کسی دیگر روی آن کار نمی کند:
- [Issues باز](https://github.com/MortezaZareiZakliki/deadlineflow/issues)
- [Pull Requests باز](https://github.com/MortezaZareiZakliki/deadlineflow/pulls)

### ۳. انتخاب یک Issue
اگر تازه کار هستید، Issues با برچسب `good first issue` را انتخاب کنید. این Issues برای شروع مناسب تر هستند.

---

## 🛠 راه‌اندازی محیط توسعه

### پیش نیازها
- Docker & Docker Compose
- Git

> برای راهنمای کامل نصب (از صفر تا صد، شامل نصب WSL و Docker)، فایل [INSTALLATION.md](INSTALLATION.md) را مطالعه کنید.

### مراحل راه اندازی

۱. **Fork کردن پروژه:** 

روی دکمه Fork در صفحه GitHub کلیک کنید.

۲. **کلون کردن Fork خودتان:**

    git clone https://github.com/YOUR_USERNAME/deadlineflow.git
    cd deadlineflow

۳. **افزودن مخزن اصلی به عنوان upstream:**

    git remote add upstream https://github.com/MortezaZareiZakliki/deadlineflow.git

۴. **اجرای پروژه با Docker:**

    cp ./src/.env.example ./src/.env
    docker-compose up -d --build
    docker-compose exec app composer install
    docker-compose exec app php artisan key:generate
    docker-compose exec app php artisan migrate:fresh --seed

۵. **نصب پکیج های Node و Build:**

    docker-compose exec app npm install
    docker-compose exec app npm run build

۶. **اجرای تست‌ها (برای اطمینان):**

    docker-compose exec app php artisan test

۷. **بررسی وضعیت CI (اختیاری):**

   پس از ارسال Pull Request، تست ها به صورت خودکار در GitHub Actions (در صورت فعال بودن) اجرا می شوند. مطمئن شوید که تمام تست‌ها پاس می شوند.

---

## 📝 قوانین کدنویسی

### استانداردهای PHP
- پیروی از **PSR-12** (استاندارد کدنویسی PHP)
- استفاده از **Laravel Pint** برای فرمت بندی کد:

        docker-compose exec app ./vendor/bin/pint

### استانداردهای کدنویسی
- **نام گذاری:** استفاده از camelCase برای متغیرها و متدها، PascalCase برای کلاس ها
- **کامنت‌گذاری:** هر متد پیچیده باید کامنت توضیحی داشته باشد.
- **Type Hinting:** تمام پارامترها و مقادیر بازگشتی باید Type داشته باشند.
- **تست:** هر فیچر جدید باید تست داشته باشد (PHPUnit).

### ساختار کد
- منطق تجاری در `Services/`
- دسترسی به دیتابیس در `Repositories/`
- اعتبارسنجی در `Http/Requests/`
- فرمت بندی خروجی در `Http/Resources/`

> برای مشاهده‌ی ساختار کامل پروژه و الگوهای طراحی، بخش «معماری» در [README.md](README.md) را ببینید.

---

## 🌿 جریان کار با Git (Git Workflow)

ما از **Git Flow ساده شده** استفاده می کنیم:

### ۱. ساخت Branch
برای فیچر جدید:

    git checkout -b feature/amazing-feature

برای رفع باگ:

    git checkout -b fix/bug-name

### ۲. Commit کردن تغییرات
از **Conventional Commits** استفاده کنید:

    feat: add Gantt chart support
    fix: resolve N+1 query in tasks list
    docs: update README installation guide
    refactor: extract TaskService logic
    test: add tests for TaskRepository

### ۳. Push و ارسال Pull Request
    git push origin feature/amazing-feature
سپس در GitHub یک Pull Request باز کنید و توضیحات کامل بنویسید.

---

## ✅ چک لیست Pull Request

قبل از ارسال PR، مطمئن شوید:
- [ ] کدها با Laravel Pint فرمت شده اند.
- [ ] تمام تست ها پاس می شوند (`php artisan test`).
- [ ] مستندات (در صورت نیاز) به روز شده اند.
- [ ] هیچ فایل اضافی (مثل `.env` یا `node_modules`) کامیت نشده.
- [ ] توضیحات PR شامل خلاصه ی تغییرات و دلیل آن است.

---

## 🐛 گزارش باگ

برای گزارش باگ، یک Issue با ساختار زیر باز کنید:

### توضیح باگ
(توضیح کوتاه و واضح)

### مراحل بازتولید
1. برو به ...
2. کلیک کن روی ...
3. خطا را مشاهده کن

### رفتار مورد انتظار
(چه چیزی باید اتفاق می افتاد؟)

### محیط
- OS: Windows 11 / macOS 14 / Ubuntu 22.04
- Docker version: ...
- PHP version: ...

### اسکرین شات (اختیاری)

---

## 💡 پیشنهاد فیچر

برای پیشنهاد فیچر جدید، یک Issue با ساختار زیر باز کنید:

### مشکل
(چه مشکلی وجود دارد؟)

### راه حل پیشنهادی
(چه فیچری می تواند این مشکل را حل کند؟)

### جایگزین ها
(آیا راه حل دیگری وجود دارد؟)

### اطلاعات اضافی
(تصاویر، لینک ها، و ...)

---

## ❤️ تشکر

هر مشارکتی، هر چقدر هم کوچک باشد (از یک اصلاح تایپو تا افزودن یک فیچر بزرگ)، ارزشمند است. از شما سپاسگزاریم که به رشد DeadlineFlow کمک می کنید!

---

<div align="center">

**با هم می‌سازیم، بهتر از دیروز. 🚀**

</div>
