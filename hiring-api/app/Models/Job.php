<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'benefits',
        'salary_min',
        'salary_max',
        'location',
        'remote',
        'type',
        'status',
        'user_id',
        'company_name',
        'deadline',
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'remote' => 'boolean',
        'deadline' => 'date',
    ];

    /**
     * Get the user (recruiter) who posted the job.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the applications for the job.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Check if job is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if job is remote
     */
    public function isRemote(): bool
    {
        return $this->remote === true;
    }
}
