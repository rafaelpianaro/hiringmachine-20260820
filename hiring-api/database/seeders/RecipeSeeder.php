<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chef = User::where('email', 'laura.sains@cuisine.com')->first() ?? User::query()->first();

        if (!$chef) {
            return;
        }

        $recipes = [
            [
                'title' => 'Homemade Chocolate Cake',
                'description' => 'A delicious fluffy and soft chocolate cake, perfect for afternoon tea.',
                'ingredients' => [
                    '2 cups all-purpose flour',
                    '1 cup sugar',
                    '1/2 cup cocoa powder',
                    '2 eggs',
                    '1 cup milk',
                    '1/2 cup vegetable oil',
                    '1 tablespoon baking powder',
                    '1 teaspoon vanilla extract',
                ],
                'instructions' => [
                    'Preheat the oven to 350°F (180°C).',
                    'In a bowl, mix the dry ingredients.',
                    'In another bowl, beat the eggs with the oil and milk.',
                    'Add the dry ingredients to the wet ones and mix well.',
                    'Add the vanilla extract and baking powder.',
                    'Pour into a greased and floured baking pan.',
                    'Bake for 35-40 minutes or until golden.',
                ],
                'prep_time' => 15,
                'cook_time' => 40,
                'servings' => 8,
                'difficulty' => 'easy',
                'category' => 'Cakes',
                'is_published' => true,
                'user_id' => $chef->id,
            ],
            // [
            //     'title' => 'Chicken Stroganoff',
            //     'description' => 'Creamy and flavorful stroganoff, a classic of Brazilian cuisine.',
            //     'ingredients' => [
            //         '500g chicken breast diced',
            //         '1 onion chopped',
            //         '2 cloves of garlic minced',
            //         '1 can tomato sauce',
            //         '1 box heavy cream',
            //         '2 tablespoons mustard',
            //         '2 tablespoons ketchup',
            //         'Salt and pepper to taste',
            //         'Oil for sautéing',
            //     ],
            //     'instructions' => [
            //         'Season the chicken and brown in a pan with oil.',
            //         'Remove the chicken and sauté the onion and garlic.',
            //         'Add the tomato sauce and cook for 5 minutes.',
            //         'Return the chicken to the pan and cook for another 10 minutes.',
            //         'Add the mustard and ketchup, mix well.',
            //         'Finally, add the heavy cream and turn off the heat.',
            //         'Serve with white rice and shoestring potatoes.',
            //     ],
            //     'prep_time' => 20,
            //     'cook_time' => 30,
            //     'servings' => 4,
            //     'difficulty' => 'easy',
            //     'category' => 'Main Dishes',
            //     'is_published' => true,
            //     'user_id' => $chef->id,
            // ],
            // [
            //     'title' => 'Sourdough Bread',
            //     'description' => 'Homemade bread made with natural sourdough, with a crispy crust and soft crumb.',
            //     'ingredients' => [
            //         '500g all-purpose flour',
            //         '350g warm water',
            //         '100g active sourdough starter',
            //         '10g salt',
            //         '1 teaspoon sugar',
            //     ],
            //     'instructions' => [
            //         'Mix the flour, water, and sourdough starter.',
            //         'Let rest for 30 minutes (autolyse).',
            //         'Add the salt and sugar, mix well.',
            //         'Perform folds every 30 minutes for 2 hours.',
            //         'Refrigerate for 12 hours.',
            //         'Remove and shape into a ball.',
            //         'Preheat the oven to 480°F (250°C) with a cast iron pot inside.',
            //         'Place the dough in the hot pot, score the top.',
            //         'Bake for 30 minutes covered, then remove the lid for another 15 minutes.',
            //     ],
            //     'prep_time' => 30,
            //     'cook_time' => 45,
            //     'servings' => 1,
            //     'difficulty' => 'hard',
            //     'category' => 'Breads',
            //     'is_published' => true,
            //     'user_id' => $chef->id,
            // ],
            // [
            //     'title' => 'Grilled Chicken Caesar Salad',
            //     'description' => 'Fresh and nutritious salad with homemade caesar dressing.',
            //     'ingredients' => [
            //         '1 chicken breast',
            //         '1 romaine lettuce',
            //         '1/2 cup croutons',
            //         '1/4 cup grated parmesan',
            //         '2 tablespoons olive oil',
            //         '1 tablespoon mustard',
            //         '1 clove of garlic',
            //         'Juice of 1 lemon',
            //         'Salt and pepper to taste',
            //     ],
            //     'instructions' => [
            //         'Season the chicken with salt, pepper, and olive oil.',
            //         'Grill the chicken until golden and fully cooked.',
            //         'Let rest and slice.',
            //         'For the dressing: mix olive oil, mustard, garlic, and lemon juice.',
            //         'Wash and chop the lettuce.',
            //         'Assemble the salad with lettuce, chicken, croutons, and cheese.',
            //         'Drizzle with caesar dressing and serve.',
            //     ],
            //     'prep_time' => 15,
            //     'cook_time' => 20,
            //     'servings' => 2,
            //     'difficulty' => 'easy',
            //     'category' => 'Salads',
            //     'is_published' => true,
            //     'user_id' => $chef->id,
            // ],
            // [
            //     'title' => 'Gourmet Brigadeiros',
            //     'description' => 'Creamy brigadeiros with Belgian chocolate, perfect for parties.',
            //     'ingredients' => [
            //         '1 can sweetened condensed milk',
            //         '3 tablespoons cocoa powder',
            //         '1 tablespoon butter',
            //         '100g semi-sweet chocolate',
            //         'Chocolate sprinkles for rolling',
            //     ],
            //     'instructions' => [
            //         'Melt the chocolate in a double boiler.',
            //         'In a pan, mix the condensed milk, cocoa, and butter.',
            //         'Add the melted chocolate and stir over low heat.',
            //         'Cook until it pulls away from the bottom of the pan.',
            //         'Let cool completely.',
            //         'Grease your hands with butter and roll into small balls.',
            //         'Roll in chocolate sprinkles and place in mini cups.',
            //     ],
            //     'prep_time' => 30,
            //     'cook_time' => 15,
            //     'servings' => 30,
            //     'difficulty' => 'medium',
            //     'category' => 'Sweets',
            //     'is_published' => true,
            //     'user_id' => $chef->id,
            // ],
        ];

        foreach ($recipes as $recipe) {
            $recipeModel = Recipe::create($recipe);

            $users = User::where('id', '!=', $recipeModel->user_id)->get();
            $ratingValues = [5, 4, 3, 5, 4];

            foreach ($users as $index => $user) {
                $recipeModel->ratings()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['stars' => $ratingValues[$index % count($ratingValues)]]
                );
            }
        }
    }
}
