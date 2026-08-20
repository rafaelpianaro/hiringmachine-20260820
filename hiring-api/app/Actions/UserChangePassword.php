<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final readonly class UserChangePassword
{
    public function handle(User $user, string $currentPassword, string $newPassword): void
    {
        DB::transaction(function () use ($user, $currentPassword, $newPassword) {
            if (! Hash::check($currentPassword, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['A senha atual está incorreta.'],
                ]);
            }

            $user->update(['password' => Hash::make($newPassword)]);
        });
    }
}
