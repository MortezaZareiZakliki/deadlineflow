<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_authenticated_user_can_list_projects(): void
    {
        Project::factory()->count(2)->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->getJson('/api/projects');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }

    public function test_user_can_create_project(): void
    {
        $data = [
            'name' => 'New Project',
            'description' => 'Test Description',
            'priority' => 'high',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->postJson('/api/projects', $data);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('projects', ['name' => 'New Project']);
    }

    public function test_user_can_update_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->putJson("/api/projects/{$project->id}", [
                             'name' => 'Updated Project',
                             'priority' => 'low',
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated Project']);
    }

    public function test_user_can_delete_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_user_cannot_update_other_users_project(): void
    {
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                        ->putJson("/api/projects/{$otherProject->id}", [
                            'name' => 'Hacked',
                            'priority' => 'high'
                        ]);

        $response->assertStatus(403);
    }
}