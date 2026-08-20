<?php

namespace App\Http\Controllers\Api;

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
        $query = Recipe::with('user:id,name');

        // Filter by published status
        if ($request->has('published')) {
            $query->where('is_published', $request->boolean('published'));
        } else {
            $query->published();
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter by user (my recipes)
        if ($request->has('my_recipes') && $request->boolean('my_recipes')) {
            $query->where('user_id', Auth::id());
        }

        $recipes = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

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
            'Receita criada com sucesso',
            201
        );
    }

    /**
     * Display the specified recipe.
     */
    public function show(Recipe $recipe)
    {
        $recipe->load(['user:id,name', 'comments' => function ($query) {
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
            return $this->forbiddenResponse('Você não tem permissão para editar esta receita.');
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
            'Receita atualizada com sucesso'
        );
    }

    /**
     * Remove the specified recipe.
     */
    public function destroy(Recipe $recipe)
    {
        // Check if user owns the recipe
        if ($recipe->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Você não tem permissão para excluir esta receita.');
        }

        $recipe->delete();

        return $this->successResponse(null, 'Receita excluída com sucesso');
    }

    /**
     * Get recipes by category.
     */
    public function byCategory(Request $request, string $category)
    {
        $recipes = Recipe::published()
            ->where('category', $category)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->successResponse($recipes);
    }

    /**
     * Get recipes by difficulty.
     */
    public function byDifficulty(Request $request, string $difficulty)
    {
        $recipes = Recipe::published()
            ->where('difficulty', $difficulty)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->successResponse($recipes);
    }

    /**
     * Get my recipes.
     */
    public function myRecipes(Request $request)
    {
        $recipes = Auth::user()->recipes()
            ->withCount('comments')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->successResponse($recipes);
    }
}
