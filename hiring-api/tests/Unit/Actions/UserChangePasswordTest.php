<?php

namespace Tests\Unit\Actions;

use App\Actions\UserChangePassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_changes_the_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('oldpassword')]);

        (new UserChangePassword)->handle($user, 'oldpassword', 'newpassword');

        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }

    public function test_it_throws_when_current_password_is_wrong(): void
    {
        $user = User::factory()->create(['password' => Hash::make('oldpassword')]);

        try {
            (new UserChangePassword)->handle($user, 'wrongpassword', 'newpassword');
            $this->fail('A ValidationException deveria ter sido lançada.');
        } catch (ValidationException $e) {
            $this->assertEquals('A senha atual está incorreta.', $e->errors()['current_password'][0]);
        }

        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }
}
