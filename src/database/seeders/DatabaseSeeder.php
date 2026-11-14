<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Version;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::updateOrCreate(
            ['email' => 'demo@deadlineflow.ir'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'avatar' => null,
            ]
        );

        $project1 = Project::create([
            'user_id' => $demoUser->id,
            'name' => 'طراحی وب سایت شرکتی',
            'description' => 'طراحی و پیاده سازی وب سایت جدید شرکت با Laravel و React.',
            'priority' => 'high',
            'start_date' => now()->subDays(10),
            'deadline' => now()->addDays(20),
        ]);

        $project2 = Project::create([
            'user_id' => $demoUser->id,
            'name' => 'اپلیکیشن موبایل TimeTrack',
            'description' => 'ساخت نسخه اندروید و iOS اپلیکیشن مدیریت زمان.',
            'priority' => 'medium',
            'start_date' => now()->subDays(5),
            'deadline' => now()->addDays(45),
        ]);

        $project3 = Project::create([
            'user_id' => $demoUser->id,
            'name' => 'کمپین بازاریابی زمستانه',
            'description' => 'برنامه ریزی و اجرای کمپین تبلیغاتی زمستانه.',
            'priority' => 'low',
            'start_date' => now()->subDays(30),
            'deadline' => now()->subDays(2),
        ]);

        $version11 = Version::create([
            'user_id' => $demoUser->id,
            'project_id' => $project1->id,
            'version_number' => '1.0.0',
            'description' => 'نسخه اولیه با صفحات اصلی',
            'release_date' => now()->addDays(10),
        ]);

        $version12 = Version::create([
            'user_id' => $demoUser->id,
            'project_id' => $project1->id,
            'version_number' => '1.1.0',
            'description' => 'اضافه شدن پنل مدیریت',
            'release_date' => now()->addDays(20),
        ]);

        $version21 = Version::create([
            'user_id' => $demoUser->id,
            'project_id' => $project2->id,
            'version_number' => '1.0.0',
            'description' => 'نسخه اولیه با صفحات اصلی',
            'release_date' => now()->addDays(10),
        ]);

        $version22 = Version::create([
            'user_id' => $demoUser->id,
            'project_id' => $project2->id,
            'version_number' => '1.1.0',
            'description' => 'اضافه شدن پنل مدیریت',
            'release_date' => now()->addDays(20),
        ]);


        Task::create([
            'user_id' => $demoUser->id,
            'project_id' => $project1->id,
            'version_id' => $version11->id,
            'title' => 'طراحی صفحه اصلی (Home Page)',
            'description' => 'پیاده سازی UI صفحه اصلی با React.',
            'status' => 'in_progress',
            'priority' => 'low',
            'deadline' => now()->subDays(2),
        ]);

        Task::create([
            'user_id' => $demoUser->id,
            'project_id' => $project2->id,
            'version_id' => $version21->id,
            'title' => 'طراحی فوتر (Footer)',
            'description' => 'پیاده سازی UI طراحی فوتر با React.',
            'status' => 'done',
            'priority' => 'high',
            'deadline' => now()->subDays(5),
        ]);

        Task::create([
            'user_id' => $demoUser->id,
            'project_id' => $project1->id,
            'version_id' => $version11->id,
            'title' => 'پیاده سازی API لاگین',
            'description' => 'استفاده از Laravel Sanctum برای احراز هویت.',
            'status' => 'in_progress',
            'priority' => 'high',
            'deadline' => now()->addDays(2),
        ]);

        Task::create([
            'user_id' => $demoUser->id,
            'project_id' => $project1->id,
            'version_id' => $version12->id,
            'title' => 'ساخت داشبورد مدیریت',
            'description' => 'نمایش آمار و ارقام کاربران.',
            'status' => 'todo',
            'priority' => 'medium',
            'deadline' => now()->addDays(15),
        ]);

        Task::create([
            'user_id' => $demoUser->id,
            'project_id' => $project2->id,
            'version_id' => $version22->id,
            'title' => 'تحقیق در مورد فریم ورک React Native',
            'description' => 'بررسی مزایا و معایب React Native برای توسعه اپلیکیشن.',
            'status' => 'todo',
            'priority' => 'low',
            'deadline' => now()->addDays(7),
        ]);
    }
}
