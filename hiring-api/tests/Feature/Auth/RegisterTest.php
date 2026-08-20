<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    public function test_user_can_register_with_valid_data()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $response->json('data.user.email'),
        ]);
    }
    public function test_user_can_register_with_user_role()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('user', $response->json('data.user.role'));
    }

    public function test_registration_rejects_admin_role()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }
    public function test_registration_requires_name()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
    public function test_registration_requires_valid_email()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => $this->faker->name,
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
    public function test_registration_requires_unique_email()
    {
        User::factory()->create(['email' => 'existing@email.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => $this->faker->name,
            'email' => 'existing@email.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
    public function test_registration_requires_password_confirmation()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
    public function test_registration_requires_minimum_password_length()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
    public function test_default_role_is_user()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('user', $response->json('data.user.role'));
    }
}
