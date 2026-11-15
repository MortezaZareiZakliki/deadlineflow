<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function getAllByUser(int $userId): Collection
    {
        return Task::whereHas('project', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->withCount(['project', 'version'])->orderBy('deadline', 'asc')->get();
    }

    public function findById(int $id): ?Task
    {
        return Task::with(['project', 'version'])->find($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function getDueSoonTasks(int $days = 1): Collection
    {
        return Task::where('status', '!=', 'done')
            ->whereDate('deadline', '>=', now())
            ->whereDate('deadline', '<=', now()->addDays($days))
            ->get();
    }

    public function getTaskStatsByUser(int $userId): array
{
    $query = Task::whereHas('project', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    });

    return [
        'todo' => (clone $query)->where('status', 'todo')->count(),
        'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
        'done' => (clone $query)->where('status', 'done')->count(),
        'total' => $query->count(),
    ];
}
}