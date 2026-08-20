<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidate = User::where('role', 'candidate')->first();
        $firstJob = Job::orderBy('id')->first();
        $secondJob = Job::orderBy('id')->offset(1)->first();
        $applicant = User::where('email', 'ana.souza@email.com')->first();

        if (! $candidate || ! $firstJob) {
            return;
        }

        if ($secondJob && $candidate) {
            Application::create([
                'user_id' => $candidate->id,
                'job_id' => $secondJob->id,
                'cover_letter' => 'Estou muito interessado nesta oportunidade.',
                'status' => 'pending',
                'applied_at' => now(),
            ]);
        }

        if ($applicant) {
            Application::create([
                'user_id' => $applicant->id,
                'job_id' => $firstJob->id,
                'cover_letter' => 'Gostaria de participar do processo seletivo.',
                'status' => 'pending',
                'applied_at' => now(),
            ]);
        }
    }
}
