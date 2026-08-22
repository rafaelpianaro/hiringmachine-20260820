<?php

namespace App\Http\Controllers\Api;

use App\Actions\RecipeList;
use App\Actions\RecipeListByCategory;
use App\Actions\RecipeListByDifficulty;
use App\Actions\RecipeRate;
use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of recipes.
     */
    public function index(Request $request)
    {
        $recipes = (new RecipeList)->handle($request);

        return $this->successResponse($recipes);
    }

    /**
     * Store a newly created recipe.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'string',
            'instructions' => 'required|array|min:1',
            'instructions.*' => 'string',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        $recipe = Auth::user()->recipes()->create($request->all());

        return $this->successResponse(
            $recipe->load('user:id,name'),
            'Recipe created successfully',
            201
        );
    }

    /**
     * Display the specified recipe.
     */
    public function show(Recipe $recipe)
    {
        $recipe->load(['user:id,name', 'ratings.user:id,name', 'comments' => function ($query) {
            $query->with('user:id,name')->latest();
        }]);

        return $this->successResponse($recipe);
    }

    /**
     * Update the specified recipe.
     */
    public function update(Request $request, Recipe $recipe)
    {
        // Check if user owns the recipe
        if ($recipe->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to edit this recipe.');
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'ingredients' => 'sometimes|array|min:1',
            'ingredients.*' => 'string',
            'instructions' => 'sometimes|array|min:1',
            'instructions.*' => 'string',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:1',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'category' => 'sometimes|string|max:255',
            'image' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        $recipe->update($request->all());

        return $this->successResponse(
            $recipe->fresh()->load('user:id,name'),
            'Recipe updated successfully.'
        );
    }

    /**
     * Remove the specified recipe.
     */
    public function destroy(Recipe $recipe)
    {
        // Check if user owns the recipe
        if ($recipe->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to delete this recipe.');
        }

        $recipe->delete();

        return $this->successResponse(null, 'Recipe deleted successfully.');
    }

    /**
     * Get recipes by category.
     */
    public function byCategory(Request $request, string $category)
    {
        $recipes = (new RecipeListByCategory)->handle($request, $category);

        return $this->successResponse($recipes);
    }

    /**
     * Get recipes by difficulty.
     */
    public function byDifficulty(Request $request, string $difficulty)
    {
        $recipes = (new RecipeListByDifficulty)->handle($request, $difficulty);

        return $this->successResponse($recipes);
    }

    /**
     * Get my recipes.
     */
    public function myRecipes(Request $request)
    {
        $recipes = (new RecipeList)->handle($request, true);

        return $this->successResponse($recipes);
    }

    /**
     * Rate a recipe.
     */
    public function rate(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'stars' => 'required|integer|min:1|max:5',
        ]);

        try {
            $payload = (new RecipeRate)->handle($recipe, Auth::id(), $validated['stars']);
        } catch (\RuntimeException $exception) {
            return $this->errorResponse($exception->getMessage(), 422);
        }

        return $this->successResponse($payload,            'Rating saved successfully', 201);
    }
}
