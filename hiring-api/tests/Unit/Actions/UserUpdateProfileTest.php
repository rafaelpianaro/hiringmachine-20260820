<?php

namespace Tests\Unit\Actions;

use App\Actions\UserUpdateProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserUpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_only_the_provided_fields(): void
    {
        $user = User::factory()->create(['name' => 'Original', 'phone' => null]);

        $updated = (new UserUpdateProfile)->handle($user, ['name' => 'Updated']);

        $this->assertEquals('Updated', $updated->name);
        $this->assertNull($updated->phone);
    }

    public function test_it_returns_a_fresh_user_instance(): void
    {
        $user = User::factory()->create(['name' => 'Original']);

        $updated = (new UserUpdateProfile)->handle($user, ['name' => 'Updated']);

        $this->assertNotSame($user, $updated);
        $this->assertTrue($updated->is($user));
        $this->assertEquals('Updated', $updated->name);
    }
}
