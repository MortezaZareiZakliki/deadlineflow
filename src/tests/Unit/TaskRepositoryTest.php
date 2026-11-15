<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Version;
use App\Repositories\Eloquent\EloquentTaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentTaskRepository $repository;
    private User $user;
    private Project $project;
    private Version $version;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentTaskRepository();
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $this->user->id]);
        $this->version = Version::factory()->create(['project_id' => $this->project->id]);
    }

    public function test_get_all_by_user_returns_only_user_tasks(): void
    {
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'version_id' => $this->version->id,
        ]);

        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
        $otherVersion = Version::factory()->create(['project_id' => $otherProject->id]);
        Task::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'project_id' => $otherProject->id,
            'version_id' => $otherVersion->id,
        ]);

        $tasks = $this->repository->getAllByUser($this->user->id);

        $this->assertCount(3, $tasks);
    }

    public function test_find_by_id_returns_correct_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'version_id' => $this->version->id,
        ]);

        $found = $this->repository->findById($task->id);

        $this->assertNotNull($found);
        $this->assertEquals($task->id, $found->id);
    }
}