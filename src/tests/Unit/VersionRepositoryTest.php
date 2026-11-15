<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use App\Models\Version;
use App\Repositories\Eloquent\EloquentVersionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VersionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentVersionRepository $repository;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentVersionRepository();
        
        $user = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $user->id]);
    }

    public function test_get_all_by_project_returns_versions_sorted_by_semver(): void
    {
        Version::factory()->create(['project_id' => $this->project->id, 'version_number' => '1.0.0']);
        Version::factory()->create(['project_id' => $this->project->id, 'version_number' => '2.1.0']);
        Version::factory()->create(['project_id' => $this->project->id, 'version_number' => '1.1.5']);

        $versions = $this->repository->getAllByProject($this->project->id);

        $this->assertCount(3, $versions);
        
        $this->assertEquals('2.1.0', $versions[0]->version_number);
        $this->assertEquals('1.1.5', $versions[1]->version_number);
        $this->assertEquals('1.0.0', $versions[2]->version_number);
    }

    public function test_find_by_id_returns_correct_version(): void
    {
        $version = Version::factory()->create(['project_id' => $this->project->id]);

        $found = $this->repository->findById($version->id);

        $this->assertNotNull($found);
        $this->assertEquals($version->id, $found->id);
    }

    public function test_create_version_persists_to_database(): void
    {
        $data = [
            'project_id' => $this->project->id,
            'version_number' => '3.0.0',
            'description' => 'Major Release',
        ];

        $version = $this->repository->create($data);

        $this->assertInstanceOf(Version::class, $version);
        $this->assertDatabaseHas('versions', ['version_number' => '3.0.0']);
    }

    public function test_delete_version_soft_deletes(): void
    {
        $version = Version::factory()->create(['project_id' => $this->project->id]);

        $result = $this->repository->delete($version);

        $this->assertTrue($result);
        $this->assertSoftDeleted('versions', ['id' => $version->id]);
    }
}