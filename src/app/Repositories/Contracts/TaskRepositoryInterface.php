<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function getAllByUser(int $userId): Collection;
    public function findById(int $id): ?Task;
    public function create(array $data): Task;
    public function update(Task $task, array $data): bool;
    public function delete(Task $task): bool;
    public function getDueSoonTasks(int $days = 1): Collection;
    public function getTaskStatsByUser(int $userId): array;
}