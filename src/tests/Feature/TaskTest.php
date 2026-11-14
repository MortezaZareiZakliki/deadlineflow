<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;
    private Version $version;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $this->user->id]);
        $this->version = Version::factory()->create(['project_id' => $this->project->id]);
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_authenticated_user_can_list_tasks(): void
    {
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'version_id' => $this->version->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->getJson('/api/tasks');
        
        $response->assertStatus(200);
        $data = $response->json();
        $tasks = $data['data']['data'] ?? $data['data'] ?? [];

        $this->assertCount(3, $tasks);
    }

    public function test_unauthenticated_user_cannot_list_tasks(): void
    {
        $response = $this->getJson('/api/tasks');
        $response->assertStatus(401);
    }

    public function test_user_can_create_task(): void
    {
        $data = [
            'project_id' => $this->project->id,
            'version_id' => $this->version->id,
            'title' => 'Test Task',
            'status' => 'todo',
            'priority' => 'high',
            'deadline' => now()->addDays(5)->format('Y-m-d'),
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->postJson('/api/tasks', $data);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $response->assertJsonPath('data.title', 'Test Task');
    }

    public function test_user_cannot_create_task_with_invalid_data(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->postJson('/api/tasks', [
                             'title' => '',
                             'priority' => 'invalid',
                         ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title', 'priority', 'project_id', 'version_id']);
    }

    public function test_user_can_update_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'version_id' => $this->version->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->putJson("/api/tasks/{$task->id}", [
                             'title' => 'Updated Task',
                             'status' => 'done',
                             'priority' => 'low',
                             'project_id' => $this->project->id,
                             'version_id' => $this->version->id,
                             'deadline' => $task->deadline,
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_user_can_delete_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'version_id' => $this->version->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_access_other_users_task(): void
    {
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
        $otherVersion = Version::factory()->create(['project_id' => $otherProject->id]);
        $otherTask = Task::factory()->create([
            'user_id' => $otherUser->id,
            'project_id' => $otherProject->id,
            'version_id' => $otherVersion->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->getJson("/api/tasks/{$otherTask->id}");

        $response->assertStatus(403);
    }
}