<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskDeadlineNotification extends Notification
{
    use Queueable;

    public Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $today = now()->startOfDay();
        $deadline = \Carbon\Carbon::parse($this->task->deadline)->startOfDay();
        $daysLeft = $today->diffInDays($deadline, false);

        if ($daysLeft < 0) {
            $message = "تسک «{$this->task->title}» ددلاینش گذشته است! لطفاً بررسی کنید.";
        } elseif ($daysLeft == 0) {
            $message = "تسک «{$this->task->title}» امروز ددلاین دارد!";
        } else {
            $message = "تسک «{$this->task->title}» تا {$daysLeft} روز آینده ددلاین دارد.";
        }

        return [
            'task_id' => $this->task->id,
            'title' => 'یادآوری ددلاین',
            'message' => $message,
            'url' => "/tasks",
        ];
    }
}