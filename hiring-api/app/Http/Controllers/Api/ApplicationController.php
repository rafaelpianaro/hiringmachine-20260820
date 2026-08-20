<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    use ApiResponser;

    /**
     * Display listings for the authenticated user.
     */
    public function index(Request $request)
    {
        $applications = Auth::user()->applications()
            ->with('job:id,title,company_name,location')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->successResponse($applications);
    }

    /**
     * Store a newly created application.
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'cover_letter' => 'nullable|string',
            'resume_path' => 'nullable|string|max:255',
        ]);

        // Check if already applied
        $exists = Application::where('user_id', Auth::id())
            ->where('job_id', $request->job_id)
            ->exists();

        if ($exists) {
            return $this->errorResponse('Você já se candidatou para esta vaga.', 409);
        }

        // Check if job is active
        $job = Job::find($request->job_id);
        if (!$job || !$job->isActive()) {
            return $this->errorResponse('Esta vaga não está mais disponível.', 404);
        }

        $application = Auth::user()->applications()->create([
            'job_id' => $request->job_id,
            'cover_letter' => $request->cover_letter,
            'resume_path' => $request->resume_path,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        return $this->successResponse(
            $application->load('job:id,title,company_name,location'),
            'Candidatura enviada com sucesso',
            201
        );
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application)
    {
        // Check if user owns the application or is the job owner
        if ($application->user_id !== Auth::id() && $application->job->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Você não tem permissão para acessar esta candidatura.');
        }

        return $this->successResponse(
            $application->load(['job:id,title,company_name,location', 'user:id,name,email'])
        );
    }

    /**
     * Update application status (for recruiters/employers).
     */
    public function updateStatus(Request $request, Application $application)
    {
        // Check if user is the job owner
        if ($application->job->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Você não tem permissão para alterar o status desta candidatura.');
        }

        $request->validate([
            'status' => 'required|in:pending,reviewed,accepted,rejected',
            'notes' => 'nullable|string',
        ]);

        $application->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return $this->successResponse(
            $application->fresh()->load(['job:id,title', 'user:id,name,email']),
            'Status da candidatura atualizado com sucesso.'
        );
    }

    /**
     * Get applications for a specific job (for recruiters/employers).
     */
    public function jobApplications(Request $request, Job $job)
    {
        // Check if user owns the job
        if ($job->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Você não tem permissão para acessar as candidaturas desta vaga.');
        }

        $applications = $job->applications()
            ->with('user:id,name,email,phone')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->successResponse($applications);
    }

    /**
     * Withdraw an application.
     */
    public function destroy(Application $application)
    {
        // Check if user owns the application
        if ($application->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Você não tem permissão para cancelar esta candidatura.');
        }

        $application->delete();

        return $this->successResponse(null, 'Candidatura cancelada com sucesso.');
    }
}
