<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ProjectService
{
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    public function listProjects(int $userId)
    {
        $cacheKey = "user_{$userId}_projects";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($userId) {
            return $this->projectRepository->getAllByUser($userId)->toArray();
        });

        return Project::hydrate($data);
    }

    public function createProject(int $userId, array $data): Project
    {
        $data['user_id'] = $userId;
        $project = $this->projectRepository->create($data);
        
        $this->clearUserCache($project->user_id);
        
        return $project;
    }

    public function updateProject(Project $project, array $data): bool
    {
        $updated = $this->projectRepository->update($project, $data);

        $this->clearUserCache($project->user_id);
        
        return $updated;
    }

    public function deleteProject(Project $project): bool
    {
        $deleted = $this->projectRepository->delete($project);

        $this->clearUserCache($project->user_id);
        
        return $deleted;
    }

    private function clearUserCache(int $userId): void
    {
        Cache::forget("user_{$userId}_projects");
    }
}