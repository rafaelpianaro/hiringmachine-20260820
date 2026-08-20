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
        $job = Job::first();

        if (! $candidate || ! $job) {
            return;
        }

        Application::create([
            'user_id' => $candidate->id,
            'job_id' => $job->id,
            'cover_letter' => 'Estou muito interessado nesta oportunidade.',
            'status' => 'pending',
            'applied_at' => now(),
        ]);
    }
}
