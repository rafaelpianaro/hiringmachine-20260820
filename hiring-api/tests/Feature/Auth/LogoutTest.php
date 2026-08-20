<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_authenticated_user_can_logout()
    {
        $user = User::where('email', 'pedro@email.com')->first();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout realizado com sucesso.',
            ]);
    }
    public function test_unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }
    public function test_token_is_invalidated_after_logout()
    {
        $user = User::where('email', 'pedro@email.com')->first();
        $token = JWTAuth::fromUser($user);

        // Logout
        $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/auth/logout');

        // Try to use the same token
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }
}
