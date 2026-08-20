<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::where('email', 'lucas.costa@email.com')->first();
        $this->token = JWTAuth::fromUser($this->user);
    }
    public function test_authenticated_user_can_get_profile()
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'role'],
            ]);
    }
    public function test_unauthenticated_user_cannot_get_profile()
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }
    public function test_authenticated_user_can_update_profile()
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson('/api/v1/auth/profile', [
                'name' => 'Pedro Updated',
                'phone' => '+55 11 00000-0000',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Perfil atualizado com sucesso.',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Pedro Updated',
            'phone' => '+55 11 00000-0000',
        ]);
    }
    public function test_authenticated_user_can_change_password()
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson('/api/v1/auth/password', [
                'current_password' => 'password',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Senha alterada com sucesso.',
            ]);
    }
    public function test_user_cannot_change_password_with_wrong_current_password()
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson('/api/v1/auth/password', [
                'current_password' => 'wrongpassword',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }
}
