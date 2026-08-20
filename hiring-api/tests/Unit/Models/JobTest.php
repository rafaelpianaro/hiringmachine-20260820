<?php

namespace Tests\Unit\Models;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_job_belongs_to_user()
    {
        $job = Job::first();

        $this->assertInstanceOf(User::class, $job->user);
    }

    public function test_job_can_be_active()
    {
        $job = Job::first();

        $this->assertTrue($job->isActive());
    }

    public function test_job_can_be_remote()
    {
        $user = User::first();

        $job = Job::create([
            'user_id' => $user->id,
            'title' => 'Remote Job',
            'description' => 'Test Description',
            'location' => 'Remote',
            'type' => 'full-time',
            'company_name' => 'Test Company',
            'remote' => true,
        ]);

        $this->assertTrue($job->isRemote());
    }

    public function test_job_has_correct_types()
    {
        $fullTime = Job::where('type', 'full-time')->first();
        $partTime = Job::where('type', 'part-time')->first();
        $contract = Job::where('type', 'contract')->first();
        $internship = Job::where('type', 'internship')->first();

        if ($fullTime) {
            $this->assertEquals('full-time', $fullTime->type);
        }
        if ($partTime) {
            $this->assertEquals('part-time', $partTime->type);
        }
        if ($contract) {
            $this->assertEquals('contract', $contract->type);
        }
        if ($internship) {
            $this->assertEquals('internship', $internship->type);
        }
    }

    public function test_job_has_salary_range()
    {
        $job = Job::whereNotNull('salary_min')->first();

        if ($job) {
            $this->assertNotNull($job->salary_min);
            $this->assertNotNull($job->salary_max);
            $this->assertGreaterThanOrEqual($job->salary_min, $job->salary_max);
        }
    }

    public function test_job_can_be_created()
    {
        $user = User::first();

        $job = Job::create([
            'user_id' => $user->id,
            'title' => 'Test Job',
            'description' => 'Test Description',
            'location' => 'Test Location',
            'type' => 'full-time',
            'company_name' => 'Test Company',
        ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Test Job',
        ]);
    }
}
