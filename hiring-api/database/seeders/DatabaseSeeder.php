<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedUsers();

        // Seed recipes, jobs and applications
        $this->call(RecipeSeeder::class);
        $this->call(JobSeeder::class);
        $this->call(ApplicationSeeder::class);
    }

    private function seedUsers(): void
    {
        $this->createUser([
            'name' => 'Rafael Pianaro',
            'email' => 'rafaelpianaro@mail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'company' => 'RecipeApp',
            'position' => 'Administrador',
            'phone' => '+55 11 99999-0000',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Chefs / autores de receitas (role: recruiter)
        $this->createUser([
            'name' => 'Maria Clara',
            'email' => 'maria.clara@culinaria.com',
            'password' => Hash::make('password'),
            'role' => 'recruiter',
            'company' => 'Casa da Maria',
            'position' => 'Chef de Cozinha',
            'phone' => '+55 11 99999-1111',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->createUser([
            'name' => 'João Pedro',
            'email' => 'joao.pedro@culinaria.com',
            'password' => Hash::make('password'),
            'role' => 'recruiter',
            'company' => 'Sabor & Arte',
            'position' => 'Confeiteiro',
            'phone' => '+55 11 99999-2222',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Usuários comuns (role: user)
        $this->createUser([
            'name' => 'Ana Souza',
            'email' => 'ana.souza@email.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '+55 11 99999-3333',
            'position' => 'Food Blogger',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->createUser([
            'name' => 'Carlos Lima',
            'email' => 'carlos.lima@email.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '+55 11 99999-4444',
            'position' => 'Amante da Culinária',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->createUser([
            'name' => 'Fernanda Rocha',
            'email' => 'fernanda.rocha@email.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '+55 11 99999-5555',
            'position' => 'Nutricionista',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Candidato (cozinha)
        $this->createUser([
            'name' => 'Lucas Costa',
            'email' => 'lucas.costa@email.com',
            'password' => Hash::make('password'),
            'role' => 'candidate',
            'phone' => '+55 11 99999-6666',
            'position' => 'Aprendiz de Cozinha',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
    }

    private function createUser(array $attributes): User
    {
        return User::updateOrCreate(
            ['email' => $attributes['email']],
            $attributes
        );
    }
}