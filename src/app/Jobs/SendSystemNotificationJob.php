<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\SystemCleanupNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendSystemNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
        public int $totalCount,
        public int $projectsCount,
        public int $versionsCount
    ) {}

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            Log::warning("User with ID {$this->userId} not found. Notification not sent.");
            return;
        }

        $user->notify(new SystemCleanupNotification(
            $this->totalCount,
            $this->projectsCount,
            $this->versionsCount
        ));

        Log::info("📧 System notification sent to user {$user->email}.");
    }
}