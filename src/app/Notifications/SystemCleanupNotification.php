<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemCleanupNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $totalCount,
        public int $projectsCount,
        public int $versionsCount
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'پاکسازی خودکار انجام شد 🧹',
            'message' => "سیستم {$this->projectsCount} پروژه و {$this->versionsCount} نسخه‌ی حذف شده ی قدیمی شما را پاکسازی کرد.",
            'url' => '/notifications',
        ];
    }
}