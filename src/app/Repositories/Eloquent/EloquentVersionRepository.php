<?php

namespace App\Repositories\Eloquent;

use App\Models\Version;
use App\Repositories\Contracts\VersionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EloquentVersionRepository implements VersionRepositoryInterface
{
    public function getAllByProject(int $projectId): Collection
    {
        $versions = Version::where('project_id', $projectId)->get();

        return $versions->sortByDesc(function ($version) {
            $parts = explode('.', $version->version_number);
            return sprintf('%03d%03d%03d', $parts[0] ?? 0, $parts[1] ?? 0, $parts[2] ?? 0);
        })->values();
    }

    public function findById(int $id): ?Version
    {
        return Version::find($id);
    }

    public function create(array $data): Version
    {
        if (!isset($data['user_id'])) {
            $project = \App\Models\Project::find($data['project_id']);
            $data['user_id'] = $project->user_id;
        }

        return Version::create($data);
    }

    public function delete(Version $version): bool
    {
        return $version->delete();
    }
}