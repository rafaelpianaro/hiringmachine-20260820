<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Recipe;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final readonly class RecipeList
{
    public function handle(Request $request, bool $myRecipes = false): LengthAwarePaginator
    {
        $query = Recipe::with(['user:id,name', 'ratings.user:id,name']);

        if ($myRecipes) {
            $query->withCount('comments')->where('user_id', Auth::id());
        } elseif ($request->has('published')) {
            $query->where('is_published', $request->boolean('published'));
        } else {
            $query->published();
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));
    }
}
