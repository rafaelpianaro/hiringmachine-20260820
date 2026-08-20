<?php

namespace Tests\Feature\Application;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApplicationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_candidate_can_apply_for_job()
    {
        $user = User::where('email', 'pedro@email.com')->first();
        $token = JWTAuth::fromUser($user);
        $job = Job::where('user_id', '!=', $user->id)->first();

        if (!$job) {
            $this->markTestSkipped('No jobs found from other users');
        }

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/applications', [
                'job_id' => $job->id,
                'cover_letter' => 'I am interested in this position',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'data' => ['id', 'status', 'cover_letter'],
            ]);

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'job_id' => $job->id,
        ]);
    }
    public function test_candidate_cannot_apply_twice_for_same_job()
    {
        $user = User::where('email', 'pedro@email.com')->first();
        $token = JWTAuth::fromUser($user);
        $job = Job::where('user_id', '!=', $user->id)->first();

        if (!$job) {
            $this->markTestSkipped('No jobs found from other users');
        }

        // First application
        $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/applications', [
                'job_id' => $job->id,
                'cover_letter' => 'First application',
            ]);

        // Second application
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/applications', [
                'job_id' => $job->id,
                'cover_letter' => 'Second application',
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'Você já se candidatou para esta vaga.',
            ]);
    }
    public function test_candidate_can_list_own_applications()
    {
        $user = User::where('email', 'pedro@email.com')->first();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/v1/applications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => ['id', 'status', 'cover_letter'],
                    ],
                ],
            ]);
    }
    public function test_recruiter_can_update_application_status()
    {
        $recruiter = User::where('email', 'maria@techcompany.com')->first();
        $token = JWTAuth::fromUser($recruiter);

        $job = $recruiter->jobs()->first();
        if (!$job) {
            $this->markTestSkipped('No jobs found for this recruiter');
        }

        $application = Application::where('job_id', $job->id)->first();
        if (!$application) {
            $this->markTestSkipped('No applications found for this job');
        }

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/v1/applications/{$application->id}/status", [
                'status' => 'reviewed',
                'notes' => 'Good candidate',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Status da candidatura atualizado com sucesso.',
            ]);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'reviewed',
        ]);
    }
    public function test_candidate_cannot_update_application_status()
    {
        $candidate = User::where('email', 'pedro@email.com')->first();
        $token = JWTAuth::fromUser($candidate);

        $application = $candidate->applications()->first();
        if (!$application) {
            $this->markTestSkipped('No applications found for this candidate');
        }

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/v1/applications/{$application->id}/status", [
                'status' => 'accepted',
            ]);

        $response->assertStatus(403);
    }
    public function test_candidate_can_withdraw_application()
    {
        $user = User::where('email', 'pedro@email.com')->first();
        $token = JWTAuth::fromUser($user);

        $application = $user->applications()->first();
        if (!$application) {
            $this->markTestSkipped('No applications found for this candidate');
        }

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/v1/applications/{$application->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Candidatura cancelada com sucesso.',
            ]);

        $this->assertDatabaseMissing('applications', [
            'id' => $application->id,
        ]);
    }
    public function test_unauthenticated_user_cannot_apply_for_job()
    {
        $job = Job::first();

        $response = $this->postJson('/api/v1/applications', [
            'job_id' => $job->id,
            'cover_letter' => 'I am interested',
        ]);

        $response->assertStatus(401);
    }
}
