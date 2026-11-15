<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VersionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $this->user->id]);
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_user_can_list_project_versions(): void
    {
        Version::factory()->count(3)->create(['project_id' => $this->project->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->getJson("/api/projects/{$this->project->id}/versions");

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_user_can_create_version(): void
    {
        $data = [
            'version_number' => '1.0.0',
            'description' => 'Initial Release',
            'release_date' => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->postJson("/api/projects/{$this->project->id}/versions", $data);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('versions', ['version_number' => '1.0.0']);
    }

    public function test_user_cannot_delete_version_with_tasks(): void
    {
        $version = Version::factory()->create(['project_id' => $this->project->id]);
        
        Task::factory()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'version_id' => $version->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->deleteJson("/api/versions/{$version->id}");

        $response->assertStatus(422);
    }

    public function test_user_can_delete_empty_version(): void
    {
        $version = Version::factory()->create(['project_id' => $this->project->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->deleteJson("/api/versions/{$version->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('versions', ['id' => $version->id]);
    }
}