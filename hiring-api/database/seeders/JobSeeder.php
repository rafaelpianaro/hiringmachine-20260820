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
                'title' => 'Chef de Cozinha',
                'description' => 'Procuramos um chef de cozinha criativo para liderar nossa brigada e desenvolver pratos autorais.',
                'requirements' => 'Experiência com gastronomia brasileira, gestão de equipe, boas práticas de higiene.',
                'benefits' => 'Vale refeição, plano de saúde, ambiente colaborativo.',
                'salary_min' => 6000,
                'salary_max' => 9000,
                'location' => 'São Paulo',
                'remote' => false,
                'type' => 'full-time',
                'status' => 'active',
                'company_name' => 'Casa da Maria',
            ],
            [
                'user_id' => $recruiter->id,
                'title' => 'Confeiteiro(a)',
                'description' => 'Vaga para confeiteiro com foco em bolos artesanais, sobremesas e fermentação natural.',
                'requirements' => 'Experiência com confeitaria, técnicas de bolo e doces finos.',
                'benefits' => 'Vale refeição, bonificação por metas.',
                'salary_min' => 4000,
                'salary_max' => 6000,
                'location' => 'Rio de Janeiro',
                'remote' => false,
                'type' => 'full-time',
                'status' => 'active',
                'company_name' => 'Sabor & Arte',
            ],
        ];

        foreach ($jobs as $job) {
            Job::create($job);
        }
    }
}
