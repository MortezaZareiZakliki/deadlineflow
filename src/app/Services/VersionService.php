<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Version;
use App\Repositories\Contracts\VersionRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Exception;

class VersionService
{
    public function __construct(
        protected VersionRepositoryInterface $versionRepository
    ) {}

    public function listVersions(int $projectId)
    {
        $cacheKey = "project_{$projectId}_versions";
        
        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($projectId) {
            return $this->versionRepository->getAllByProject($projectId)->toArray();
        });

        return Version::hydrate($data);
    }

    public function createVersion(int $projectId, array $data): Version
    {
        $project = Project::findOrFail($projectId);
        
        $data['project_id'] = $projectId;
        $data['user_id'] = $project->user_id;
        $version = $this->versionRepository->create($data);
        
        $this->clearProjectCache($projectId);
        
        return $version;
    }

    public function deleteVersion(Version $version): bool
    {
        if ($version->tasks()->count() > 0) {
            throw new Exception('این نسخه دارای تسک است و قابل حذف نیست. ابتدا تسک‌ها را جابه‌جا یا حذف کنید.', 422);
        }

        $deleted = $this->versionRepository->delete($version);
        
        if ($deleted) {
            $this->clearProjectCache($version->project_id);
        }
        
        return $deleted;
    }

    private function clearProjectCache(int $projectId): void
    {
        Cache::forget("project_{$projectId}_versions");
    }
}