<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'ingredients',
        'instructions',
        'prep_time',
        'cook_time',
        'servings',
        'difficulty',
        'category',
        'image',
        'is_published',
        'user_id',
    ];

    protected $casts = [
        'prep_time' => 'integer',
        'cook_time' => 'integer',
        'servings' => 'integer',
        'is_published' => 'boolean',
        'ingredients' => 'array',
        'instructions' => 'array',
    ];

    /**
     * Get the user who owns the recipe.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the recipe.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the ratings for the recipe.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(RecipeRating::class);
    }

    /**
     * Check if recipe is published
     */
    public function isPublished(): bool
    {
        return $this->is_published === true;
    }

    /**
     * Get total time
     */
    public function getTotalTimeAttribute(): int
    {
        return ($this->prep_time ?? 0) + ($this->cook_time ?? 0);
    }

    /**
     * Scope to get only published recipes
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
