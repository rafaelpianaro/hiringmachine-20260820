<?php

namespace Tests\Feature\Recipe;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RecipeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_user_can_list_recipes()
    {
        $user = User::where('email', 'lucas.costa@email.com')->first();
        $token = JWTAuth::fromUser($user);
        $recipe = Recipe::first();

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/recipes/{$recipe->id}/ratings", [
                'stars' => 5,
            ]);

        $response = $this->getJson('/api/v1/recipes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => ['id', 'title', 'description', 'ingredients', 'instructions', 'ratings'],
                    ],
                ],
            ])
            ->assertJsonPath('data.data.0.ratings.0.stars', 5);
    }
    public function test_user_can_get_recipe_by_id()
    {
        $recipe = Recipe::first();

        $response = $this->getJson("/api/v1/recipes/{$recipe->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => ['id', 'title', 'description', 'ingredients', 'instructions'],
            ]);
    }
    public function test_user_can_create_recipe()
    {
        $user = User::where('email', 'lucas.costa@email.com')->first();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/recipes', [
                'title' => 'Test Recipe',
                'description' => 'A test recipe description',
                'ingredients' => ['Ingredient 1', 'Ingredient 2'],
                'instructions' => ['Step 1', 'Step 2'],
                'prep_time' => 10,
                'cook_time' => 20,
                'servings' => 4,
                'difficulty' => 'easy',
                'category' => 'Test Category',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'data' => ['id', 'title', 'description'],
            ]);

        $this->assertDatabaseHas('recipes', [
            'title' => 'Test Recipe',
            'user_id' => $user->id,
        ]);
    }
    public function test_unauthenticated_user_cannot_create_recipe()
    {
        $response = $this->postJson('/api/v1/recipes', [
            'title' => 'Test Recipe',
            'description' => 'A test recipe description',
            'ingredients' => ['Ingredient 1'],
            'instructions' => ['Step 1'],
            'difficulty' => 'easy',
            'category' => 'Test Category',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_their_recipes()
    {
        $user = User::where('email', 'maria.clara@culinaria.com')->first();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/recipes/my-recipes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => ['id', 'title', 'description', 'ingredients', 'instructions', 'created_at', 'created_at_formatted'],
                    ],
                ],
            ]);
    }

    public function test_authenticated_user_can_rate_recipe()
    {
        $user = User::where('email', 'lucas.costa@email.com')->first();
        $token = JWTAuth::fromUser($user);
        $recipe = Recipe::first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/recipes/{$recipe->id}/ratings", [
                'stars' => 5,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'data' => ['id', 'ratings'],
            ])
            ->assertJsonPath('data.ratings.0.stars', 5);

        $this->assertDatabaseHas('recipe_ratings', [
            'recipe_id' => $recipe->id,
            'user_id' => $user->id,
            'stars' => 5,
        ]);
    }

    public function test_unauthenticated_user_cannot_rate_recipe()
    {
        $recipe = Recipe::first();

        $response = $this->postJson("/api/v1/recipes/{$recipe->id}/ratings", [
            'stars' => 5,
        ]);

        $response->assertStatus(401);
    }

    public function test_user_cannot_rate_their_own_recipe()
    {
        $user = User::where('email', 'maria.clara@culinaria.com')->first();
        $token = JWTAuth::fromUser($user);
        $recipe = $user->recipes()->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/recipes/{$recipe->id}/ratings", [
                'stars' => 5,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Você não pode avaliar sua própria receita.');
    }

    public function test_user_can_update_own_recipe()
    {
        $user = User::where('email', 'maria.clara@culinaria.com')->first();
        $token = JWTAuth::fromUser($user);
        $recipe = $user->recipes()->first();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/v1/recipes/{$recipe->id}", [
                'title' => 'Updated Recipe Title',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Receita atualizada com sucesso.',
            ]);

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'title' => 'Updated Recipe Title',
        ]);
    }
    public function test_user_cannot_update_other_users_recipe()
    {
        $user = User::where('email', 'lucas.costa@email.com')->first();
        $token = JWTAuth::fromUser($user);
        $recipe = Recipe::where('user_id', '!=', $user->id)->first();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/v1/recipes/{$recipe->id}", [
                'title' => 'Hacked Recipe',
            ]);

        $response->assertStatus(403);
    }
    public function test_user_can_delete_own_recipe()
    {
        $user = User::where('email', 'maria.clara@culinaria.com')->first();
        $token = JWTAuth::fromUser($user);
        $recipe = $user->recipes()->first();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/v1/recipes/{$recipe->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Receita excluída com sucesso.',
            ]);

        $this->assertDatabaseMissing('recipes', [
            'id' => $recipe->id,
        ]);
    }
    public function test_user_can_filter_recipes_by_category()
    {
        $response = $this->getJson('/api/v1/recipes/category/Bolos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
            ]);
    }
    public function test_user_can_filter_recipes_by_difficulty()
    {
        $response = $this->getJson('/api/v1/recipes/difficulty/easy');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
            ]);
    }
    public function test_user_can_search_recipes()
    {
        $response = $this->getJson('/api/v1/recipes?search=Chocolate');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
            ]);
    }
}
