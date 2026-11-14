<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTest extends TestCase
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

    public function test_user_can_list_sessions(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->getJson('/api/sessions');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_user_can_delete_a_session(): void
    {
        $otherToken = $this->user->createToken('other_device')->accessToken;
        
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->deleteJson("/api/sessions/{$otherToken->id}");

        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $otherToken->id]);
    }

    public function test_user_cannot_delete_someone_elses_session(): void
    {
        $otherUser = User::factory()->create();
        $otherToken = $otherUser->createToken('other_user_device')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->deleteJson("/api/sessions/{$otherToken->id}");

        $response->assertStatus(404);
    }
}