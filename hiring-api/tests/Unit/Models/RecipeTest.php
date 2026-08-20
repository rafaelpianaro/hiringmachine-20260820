<?php

namespace Tests\Unit\Models;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_recipe_belongs_to_user()
    {
        $recipe = Recipe::first();

        $this->assertInstanceOf(User::class, $recipe->user);
    }
    public function test_recipe_can_be_published()
    {
        $recipe = Recipe::first();

        $this->assertTrue($recipe->isPublished());
    }
    public function test_recipe_has_total_time()
    {
        $recipe = Recipe::first();

        $totalTime = $recipe->prep_time + $recipe->cook_time;

        $this->assertEquals($totalTime, $recipe->total_time);
    }
    public function test_recipe_has_ingredients_as_array()
    {
        $recipe = Recipe::first();

        $this->assertIsArray($recipe->ingredients);
        $this->assertNotEmpty($recipe->ingredients);
    }
    public function test_recipe_has_instructions_as_array()
    {
        $recipe = Recipe::first();

        $this->assertIsArray($recipe->instructions);
        $this->assertNotEmpty($recipe->instructions);
    }
    public function test_recipe_can_be_created()
    {
        $user = User::first();

        $recipe = Recipe::create([
            'user_id' => $user->id,
            'title' => 'Test Recipe',
            'description' => 'Test Description',
            'ingredients' => ['Ingredient 1', 'Ingredient 2'],
            'instructions' => ['Step 1', 'Step 2'],
            'prep_time' => 10,
            'cook_time' => 20,
            'servings' => 4,
            'difficulty' => 'easy',
            'category' => 'Test Category',
        ]);

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'title' => 'Test Recipe',
        ]);
    }
    public function test_recipe_can_be_scoped_by_published()
    {
        $publishedRecipes = Recipe::published()->get();

        foreach ($publishedRecipes as $recipe) {
            $this->assertTrue($recipe->is_published);
        }
    }
    public function test_recipe_has_different_difficulties()
    {
        $easy = Recipe::where('difficulty', 'easy')->first();
        $medium = Recipe::where('difficulty', 'medium')->first();
        $hard = Recipe::where('difficulty', 'hard')->first();

        if ($easy) $this->assertEquals('easy', $easy->difficulty);
        if ($medium) $this->assertEquals('medium', $medium->difficulty);
        if ($hard) $this->assertEquals('hard', $hard->difficulty);
    }
}
