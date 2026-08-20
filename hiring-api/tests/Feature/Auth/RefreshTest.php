<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_authenticated_user_can_refresh_token()
    {
        $user = User::where('email', 'lucas.costa@email.com')->first();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ])
            ->assertJsonPath('data.user.id', $user->id);
    }

    public function test_unauthenticated_user_cannot_refresh_token()
    {
        $response = $this->postJson('/api/v1/auth/refresh');

        $response->assertStatus(401);
    }

    public function test_old_token_is_invalidated_after_refresh()
    {
        $user = User::where('email', 'lucas.costa@email.com')->first();
        $token = JWTAuth::fromUser($user);

        $refresh = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/auth/refresh');

        $refresh->assertStatus(200);

        $newToken = $refresh->json('data.access_token');
        $this->assertNotEquals($token, $newToken);

        $this->assertTrue(JWTAuth::parseToken()->setToken($newToken)->check());

        $this->expectException(TokenBlacklistedException::class);

        JWTAuth::parseToken()->setToken($token)->getPayload();
    }
}
