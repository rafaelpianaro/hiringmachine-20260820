<?php

namespace Tests\Unit\Actions;

use App\Actions\UserCreate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_user_with_default_role(): void
    {
        $user = (new UserCreate)->handle([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_it_sets_optional_fields_when_provided(): void
    {
        $user = (new UserCreate)->handle([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'phone' => '+55 11 99999-9999',
            'company' => 'ACME',
            'position' => 'Dev',
        ]);

        $this->assertEquals('+55 11 99999-9999', $user->phone);
        $this->assertEquals('ACME', $user->company);
        $this->assertEquals('Dev', $user->position);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '+55 11 99999-9999',
            'company' => 'ACME',
            'position' => 'Dev',
        ]);
    }

    public function test_it_leaves_optional_fields_null_when_omitted(): void
    {
        $user = (new UserCreate)->handle([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertNull($user->phone);
        $this->assertNull($user->company);
        $this->assertNull($user->position);
    }
}
