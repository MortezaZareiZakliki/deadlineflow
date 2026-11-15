<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\TaskDeadlineNotification;
use App\Models\Task;
use App\Models\Project;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
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

    public function test_user_can_list_notifications(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->getJson('/api/notifications');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);
        $version = Version::factory()->create(['project_id' => $project->id]);
        $task = Task::factory()->create(['project_id' => $project->id, 'version_id' => $version->id]);
        
        $this->user->notify(new TaskDeadlineNotification($task));
        $notification = $this->user->notifications()->first();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->postJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);
        $version = Version::factory()->create(['project_id' => $project->id]);
        $task = Task::factory()->create(['project_id' => $project->id, 'version_id' => $version->id]);
        
        $this->user->notify(new TaskDeadlineNotification($task));
        $this->user->notify(new TaskDeadlineNotification($task));

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->postJson('/api/notifications/read-all');

        $response->assertStatus(200);
        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    public function test_user_can_delete_notification(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);
        $version = Version::factory()->create(['project_id' => $project->id]);
        $task = Task::factory()->create(['project_id' => $project->id, 'version_id' => $version->id]);
        
        $this->user->notify(new TaskDeadlineNotification($task));
        $notification = $this->user->notifications()->first();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->deleteJson("/api/notifications/{$notification->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }
}