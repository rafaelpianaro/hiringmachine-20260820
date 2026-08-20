<?php

namespace Tests\Unit\Actions;

use App\Actions\UserLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_it_returns_user_with_valid_credentials(): void
    {
        $expected = User::where('email', 'lucas.costa@email.com')->first();

        $user = (new UserLogin)->handle('lucas.costa@email.com', 'password');

        $this->assertNotNull($user);
        $this->assertTrue($expected->is($user));
        $this->assertEquals('lucas.costa@email.com', $user->email);
    }

    public function test_it_returns_null_with_wrong_password(): void
    {
        $this->assertNull((new UserLogin)->handle('lucas.costa@email.com', 'wrongpassword'));
    }

    public function test_it_returns_null_with_nonexistent_email(): void
    {
        $this->assertNull((new UserLogin)->handle('nobody@email.com', 'password'));
    }

    public function test_it_returns_user_even_when_inactive(): void
    {
        $user = User::where('email', 'lucas.costa@email.com')->first();
        $user->update(['is_active' => false]);

        $returned = (new UserLogin)->handle('lucas.costa@email.com', 'password');

        $this->assertNotNull($returned);
        $this->assertFalse($returned->is_active);
    }
}
