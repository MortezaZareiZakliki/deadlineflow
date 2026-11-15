<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Eloquent\EloquentProjectRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentProjectRepository $repository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentProjectRepository();
        $this->user = User::factory()->create();
    }

    public function test_get_all_by_user_returns_only_user_projects(): void
    {
        Project::factory()->count(2)->create(['user_id' => $this->user->id]);
        
        $otherUser = User::factory()->create();
        Project::factory()->create(['user_id' => $otherUser->id]);

        $projects = $this->repository->getAllByUser($this->user->id);

        $this->assertCount(2, $projects);
    }

    public function test_find_by_id_returns_correct_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $found = $this->repository->findById($project->id);

        $this->assertNotNull($found);
        $this->assertEquals($project->id, $found->id);
    }

    public function test_find_by_id_returns_null_for_nonexistent_project(): void
    {
        $found = $this->repository->findById(99999);
        $this->assertNull($found);
    }

    public function test_create_project_persists_to_database(): void
    {
        $data = [
            'user_id' => $this->user->id,
            'name' => 'New Project',
            'priority' => 'high',
        ];

        $project = $this->repository->create($data);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertDatabaseHas('projects', ['name' => 'New Project']);
    }

    public function test_delete_project_soft_deletes(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $result = $this->repository->delete($project);

        $this->assertTrue($result);
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }
}