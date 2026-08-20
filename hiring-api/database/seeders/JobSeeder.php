<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recruiter = User::where('role', 'recruiter')->first();

        if (! $recruiter) {
            return;
        }

        $jobs = [
            [
                'user_id' => $recruiter->id,
                'title' => 'Desenvolvedor Backend PHP',
                'description' => 'Estamos buscando um desenvolvedor backend com experiência em PHP e Laravel.',
                'requirements' => 'Experiência com Laravel, PostgreSQL, APIs REST.',
                'benefits' => 'Vale alimentação, plano de saúde, home office.',
                'salary_min' => 9000,
                'salary_max' => 14000,
                'location' => 'São Paulo',
                'remote' => true,
                'type' => 'full-time',
                'status' => 'active',
                'company_name' => 'Tech Company',
            ],
            [
                'user_id' => $recruiter->id,
                'title' => 'UX Designer',
                'description' => 'Vaga para designer com foco em experiência do usuário e prototipagem.',
                'requirements' => 'Experiência com Figma, wireframes e testes de usabilidade.',
                'benefits' => 'Vale refeição, bônus trimestral.',
                'salary_min' => 7000,
                'salary_max' => 10000,
                'location' => 'Rio de Janeiro',
                'remote' => false,
                'type' => 'full-time',
                'status' => 'active',
                'company_name' => 'Startup Inc',
            ],
        ];

        foreach ($jobs as $job) {
            Job::create($job);
        }
    }
}
