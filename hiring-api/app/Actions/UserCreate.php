<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class UserCreate
{
    public function handle(array $data): User
    {
        return DB::transaction(fn () => User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'position' => $data['position'] ?? null,
        ]));
    }
}
