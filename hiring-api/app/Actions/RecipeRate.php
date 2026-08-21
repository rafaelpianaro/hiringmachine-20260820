<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Recipe;
use App\Models\RecipeRating;
use Illuminate\Support\Facades\DB;

final readonly class RecipeRate
{
    /**
     * Save or update a recipe rating for the given user.
     */
    public function handle(Recipe $recipe, int $userId, int $stars): array
    {
        if ($recipe->user_id === $userId) {
            throw new \RuntimeException('Você não pode avaliar sua própria receita.');
        }

        return DB::transaction(function () use ($recipe, $userId, $stars): array {
            $recipe->ratings()->updateOrCreate(
                ['user_id' => $userId],
                ['stars' => $stars]
            );

            $recipe->load(['user:id,name', 'ratings.user:id,name']);

            $payload = $recipe->toArray();
            $payload['user'] = [
                'id' => $recipe->user?->id,
                'name' => $recipe->user?->name,
            ];
            $payload['ratings'] = $recipe->ratings
                ->map(function (RecipeRating $ratingModel): array {
                    return [
                        'id' => $ratingModel->id,
                        'userId' => $ratingModel->user_id,
                        'stars' => $ratingModel->stars,
                        'userName' => $ratingModel->user?->name,
                    ];
                })
                ->values()
                ->all();

            return $payload;
        });
    }
}
