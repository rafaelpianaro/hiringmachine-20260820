<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Recipe;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

final readonly class RecipeListByCategory
{
    public function handle(Request $request, string $category): LengthAwarePaginator
    {
        return Recipe::published()
            ->where('category', $category)
            ->with(['user:id,name', 'ratings.user:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));
    }
}
