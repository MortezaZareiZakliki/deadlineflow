<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use App\Jobs\SendDeadlineNotificationJob;
use App\Models\Task;
use Illuminate\Console\Command;

#[Signature('tasks:send-reminders {--days=1 : تعداد روزهای آینده}')]
#[Description('ارسال یادآوری برای تسک هایی که ددلاینشون نزدیک است')]
class SendDeadlineReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        $this->info("🔍 در حال جستجوی تسک های با ددلاین تا {$days} روز آینده...");

        $startDate = now()->subDays(30)->startOfDay();
        $endDate = now()->addDays($days)->endOfDay();

        $tasks = Task::where('status', '!=', 'done')
            ->whereBetween('deadline', [$startDate, $endDate])
            ->whereNull('deadline_notified_at')
            ->where('deadline_notified_at', null)
            ->with(['user'])
            ->get();

        $totalTasks = Task::where('status', '!=', 'done')->count();
        $this->info("📊 کل تسک های فعال: {$totalTasks} | تسک های با ددلاین نزدیک: {$tasks->count()}");

        if ($tasks->isEmpty()) {
            $this->warn('⚠️ هیچ تسکی با ددلاین نزدیک پیدا نشد.');
            return Command::SUCCESS;
        }

        $this->info("📋 {$tasks->count()} تسک پیدا شد. در حال ارسال به صف...");

        foreach ($tasks as $task) {
            SendDeadlineNotificationJob::dispatch($task)->onConnection('rabbitmq');

            $task->update(['deadline_notified_at' => now()]);

            $this->line("  ✓ تسک: {$task->title} (ددلاین: {$task->deadline->format('Y-m-d')})");
        }

        $this->info('✅ همه تسک ها به صف اضافه شدند.');
        return Command::SUCCESS;
    }
}
