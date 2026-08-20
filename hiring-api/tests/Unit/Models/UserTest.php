<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_user_can_be_created()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'candidate',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
    public function test_user_has_roles()
    {
        $admin = User::where('role', 'admin')->first();
        $recruiter = User::where('role', 'recruiter')->first();
        $candidate = User::where('role', 'candidate')->first();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isRecruiter());
        $this->assertFalse($admin->isCandidate());

        $this->assertTrue($recruiter->isRecruiter());
        $this->assertFalse($recruiter->isAdmin());

        $this->assertTrue($candidate->isCandidate());
        $this->assertFalse($candidate->isAdmin());
    }
    public function test_user_has_jwt_identifier()
    {
        $user = User::first();

        $this->assertEquals($user->getKey(), $user->getJWTIdentifier());
    }
    public function test_user_has_jwt_custom_claims()
    {
        $user = User::first();

        $claims = $user->getJWTCustomClaims();

        $this->assertArrayHasKey('role', $claims);
        $this->assertArrayHasKey('name', $claims);
        $this->assertArrayHasKey('email', $claims);
        $this->assertEquals($user->role, $claims['role']);
    }
    public function test_user_password_is_hidden()
    {
        $user = User::first();

        $toArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $toArray);
    }
    public function test_user_can_be_active()
    {
        $user = User::first();

        $user->update(['is_active' => true]);
        $this->assertTrue($user->fresh()->is_active);

        $user->update(['is_active' => false]);
        $this->assertFalse($user->fresh()->is_active);
    }
}
