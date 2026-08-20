<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of jobs.
     */
    public function index(Request $request)
    {
        $query = Job::where('status', 'active');

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by remote
        if ($request->has('remote')) {
            $query->where('remote', $request->boolean('remote'));
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by salary range
        if ($request->has('salary_min')) {
            $query->where('salary_max', '>=', $request->salary_min);
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $jobs = $query->with('user:id,name,company')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->successResponse($jobs);
    }

    /**
     * Store a newly created job.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'location' => 'required|string|max:255',
            'remote' => 'boolean',
            'type' => 'required|in:full-time,part-time,contract,internship',
            'company_name' => 'required|string|max:255',
            'deadline' => 'nullable|date|after:today',
        ]);

        $job = Auth::user()->jobs()->create($request->all());

        return $this->successResponse($job->load('user:id,name,company'), 'Vaga criada com sucesso', 201);
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job)
    {
        $job->load('user:id,name,company');

        return $this->successResponse($job);
    }

    /**
     * Update the specified job.
     */
    public function update(Request $request, Job $job)
    {
        // Check if user owns the job
        if ($job->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Você não tem permissão para editar esta vaga.');
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'location' => 'sometimes|string|max:255',
            'remote' => 'boolean',
            'type' => 'sometimes|in:full-time,part-time,contract,internship',
            'status' => 'sometimes|in:active,inactive,closed',
            'company_name' => 'sometimes|string|max:255',
            'deadline' => 'nullable|date',
        ]);

        $job->update($request->all());

        return $this->successResponse($job->fresh()->load('user:id,name,company'), 'Vaga atualizada com sucesso');
    }

    /**
     * Remove the specified job.
     */
    public function destroy(Job $job)
    {
        // Check if user owns the job
        if ($job->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Você não tem permissão para excluir esta vaga.');
        }

        $job->delete();

        return $this->successResponse(null, 'Vaga excluída com sucesso');
    }

    /**
     * Get jobs posted by the authenticated user.
     */
    public function myJobs(Request $request)
    {
        $jobs = Auth::user()->jobs()
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->successResponse($jobs);
    }
}
