<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
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

    public function test_user_can_update_name(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->putJson('/api/profile/info', [
                             'name' => 'Updated Name',
                         ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'name' => 'Updated Name']);
    }

    public function test_user_can_update_password(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->putJson('/api/profile/password', [
                             'current_password' => 'password',
                             'new_password' => 'aB3$kL9#mN2@xY7!qW',
                             'new_password_confirmation' => 'aB3$kL9#mN2@xY7!qW',
                         ]);

        $response->assertStatus(200);
        
        $this->assertTrue(Hash::check('aB3$kL9#mN2@xY7!qW', $this->user->fresh()->password));
    }

    public function test_user_cannot_update_password_with_wrong_current_password(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->putJson('/api/profile/password', [
                             'current_password' => 'wrong-password',
                             'new_password' => 'aB3$kL9#mN2@xY7!qW',
                             'new_password_confirmation' => 'aB3$kL9#mN2@xY7!qW',
                         ]);

        $response->assertStatus(422);
    }
}