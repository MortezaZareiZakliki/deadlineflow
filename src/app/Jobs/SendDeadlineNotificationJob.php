<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskDeadlineNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendDeadlineNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function handle(): void
    {
        //Log::info("🔵 Job started for task ID: {$this->task->id}");

        $user = User::find($this->task->user_id);
        
        if (!$user) {
            Log::error("🔴 User not found for task ID: {$this->task->id}");
            return;
        }

        //Log::info("🟢 User found: ID={$user->id}");

        try {
            $user->notify(new TaskDeadlineNotification($this->task));
            //Log::info("✅ Notification sent for user {$user->id} and task {$this->task->id}");
        } catch (\Exception $e) {
            Log::error("🔴 Failed to send notification: " . $e->getMessage());
            return;
        }

        $this->task->update(['deadline_notified_at' => now()]);
        Log::info("📝 Task {$this->task->id} updated");
    }
}