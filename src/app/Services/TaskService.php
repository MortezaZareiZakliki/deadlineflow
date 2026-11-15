<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepository
    ) {}

    public function listTasks(int $userId)
    {
        $cacheKey = "user_{$userId}_tasks";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($userId) {
            return $this->taskRepository->getAllByUser($userId)->toArray();
        });

        return Task::hydrate($data);
    }

    public function createTask(int $userId, array $data): Task
    {
        $data['user_id'] = $userId;
        $task = $this->taskRepository->create($data);
        $this->clearUserStatCache($task->user_id);
        $this->clearUserCache($userId);
        return $task;
    }

    public function updateTask(Task $task, array $data): bool
    {
        $oldDeadline = $task->deadline?->format('Y-m-d');
        $newDeadline = isset($data['deadline']) ? \Carbon\Carbon::parse($data['deadline'])->format('Y-m-d') : null;

        if ($newDeadline !== null && $newDeadline !== $oldDeadline) {
            $data['deadline_notified_at'] = null;
        }
        
        $updated = $this->taskRepository->update($task, $data);
        $this->clearUserStatCache($task->user_id);
        $this->clearUserCache($task->user_id);
        return $updated;
    }

    public function deleteTask(Task $task): bool
    {
        $deleted = $this->taskRepository->delete($task);
        $this->clearUserStatCache($task->user_id);
        $this->clearUserCache($task->user_id);
        return $deleted;
    }

    private function clearUserCache(int $userId): void
    {
        Cache::forget("user_{$userId}_tasks");
    }

    private function clearUserStatCache(int $userId): void
    {
        Cache::forget("user_{$userId}_stats");
    }

    public function getStats(int $userId): array
    {
        $cacheKey = "user_{$userId}_stats";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId) {
            return $this->taskRepository->getTaskStatsByUser($userId);
        });
    }

    public function getUpcomingTasks(int $userId, int $days = 3)
    {
        return $this->taskRepository->getAllByUser($userId)
            ->filter(function ($task) use ($days) {
                return $task->deadline
                    && $task->status !== 'done'
                    && $task->deadline->isBetween(now(), now()->addDays($days));
            })
            ->take(5);
    }
}
