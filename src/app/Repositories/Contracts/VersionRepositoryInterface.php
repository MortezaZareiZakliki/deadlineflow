<?php

namespace App\Repositories\Contracts;

use App\Models\Version;
use Illuminate\Database\Eloquent\Collection;

interface VersionRepositoryInterface
{
    public function getAllByProject(int $projectId): Collection;
    public function findById(int $id): ?Version;
    public function create(array $data): Version;
    public function delete(Version $version): bool;
}