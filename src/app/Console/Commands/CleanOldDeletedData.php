<?php

namespace App\Console\Commands;

use App\Jobs\SendSystemNotificationJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Version;
use App\Models\User;

#[Signature('app:clean-old-data')]
#[Description('پاکسازی کامل داده های حذف شده قدیمی و اطلاع رسانی به کاربران')]
class CleanOldDeletedData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = now()->subMinutes(1);
        $totalDeleted = 0;

        User::chunk(100, function ($users) use ($date, &$totalDeleted) {
            foreach ($users as $user) {
                $deletedProjects = Project::onlyTrashed()
                    ->where('user_id', $user->id)
                    ->where('deleted_at', '<', $date)
                    ->get();

                $deletedVersions = Version::onlyTrashed()
                    ->where('user_id', $user->id)
                    ->where('deleted_at', '<', $date)
                    ->get();

                $count = $deletedProjects->count() + $deletedVersions->count();

                if ($count > 0) {
                    foreach ($deletedProjects as $project) {
                        $project->forceDelete();
                    }
                    foreach ($deletedVersions as $version) {
                        $version->forceDelete();
                    }

                    $totalDeleted += $count;

                    SendSystemNotificationJob::dispatch(
                        $user->id,
                        $count,
                        $deletedProjects->count(),
                        $deletedVersions->count()
                    )->onConnection('rabbitmq');
                    
                    $this->info("کاربر {$user->email}: {$count} آیتم پاکسازی شد و اعلان ارسال گردید.");
                }
            }
        });

        $this->info("✅ عملیات پاکسازی به پایان رسید. مجموع آیتم های حذف شده: {$totalDeleted}");
    }
}
