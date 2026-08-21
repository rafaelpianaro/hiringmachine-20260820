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
        $chef = User::where('email', 'maria.clara@culinaria.com')->first() ?? User::query()->first();

        if (!$chef) {
            return;
        }

        $recipes = [
            [
                'title' => 'Bolo de Chocolate Caseiro',
                'description' => 'Um delicioso bolo de chocolate fofinho e macio, perfeito para o café da tarde.',
                'ingredients' => [
                    '2 xícaras de farinha de trigo',
                    '1 xícara de açúcar',
                    '1/2 xícara de cacau em pó',
                    '2 ovos',
                    '1 xícara de leite',
                    '1/2 xícara de óleo',
                    '1 colher de sopa de fermento em pó',
                    '1 colher de chá de essência de baunilha',
                ],
                'instructions' => [
                    'Pré-aqueça o forno a 180°C.',
                    'Em uma tigela, misture os ingredientes secos.',
                    'Em outra tigela, bata os ovos com o óleo e o leite.',
                    'Adicione os ingredientes secos aos molhados e misture bem.',
                    'Adicione a essência de baunilha e o fermento.',
                    'Despeje em uma forma untada e enfarinhada.',
                    'Asse por 35-40 minutos ou até dourar.',
                ],
                'prep_time' => 15,
                'cook_time' => 40,
                'servings' => 8,
                'difficulty' => 'easy',
                'category' => 'Bolos',
                'is_published' => true,
                'user_id' => $chef->id,
            ],
            // [
            //     'title' => 'Strogonoff de Frango',
            //     'description' => 'Strogonoff cremoso e saboroso, um clássico da culinária brasileira.',
            //     'ingredients' => [
            //         '500g de peito de frango cortado em cubos',
            //         '1 cebola picada',
            //         '2 dentes de alho picados',
            //         '1 lata de molho de tomate',
            //         '1 caixa de creme de leite',
            //         '2 colheres de mostarda',
            //         '2 colheres de ketchup',
            //         'Sal e pimenta a gosto',
            //         'Óleo para refogar',
            //     ],
            //     'instructions' => [
            //         'Salpimente o frango e doure em uma panela com óleo.',
            //         'Retire o frango e refogue a cebola e o alho.',
            //         'Adicione o molho de tomate e cozinhe por 5 minutos.',
            //         'Retorne o frango à panela e cozinhe por mais 10 minutos.',
            //         'Adicione a mostarda e o ketchup, misture bem.',
            //         'Por último, adicione o creme de leite e desligue o fogo.',
            //         'Sirva com arroz branco e batata palha.',
            //     ],
            //     'prep_time' => 20,
            //     'cook_time' => 30,
            //     'servings' => 4,
            //     'difficulty' => 'easy',
            //     'category' => 'Pratos Principais',
            //     'is_published' => true,
            //     'user_id' => $chef->id,
            // ],
            // [
            //     'title' => 'Pão de Fermentação Natural',
            //     'description' => 'Pão caseiro feito com fermento natural, com casca crocante e miolo macio.',
            //     'ingredients' => [
            //         '500g de farinha de trigo',
            //         '350g de água morna',
            //         '100g de fermento natural ativo',
            //         '10g de sal',
            //         '1 colher de chá de açúcar',
            //     ],
            //     'instructions' => [
            //         'Misture a farinha, água e fermento natural.',
            //         'Deixe descansar por 30 minutos (autólise).',
            //         'Adicione o sal e o açúcar, misture bem.',
            //         'Faça dobras a cada 30 minutos por 2 horas.',
            //         'Leve à geladeira por 12 horas.',
            //         'Retire e molde em forma de bola.',
            //         'Pré-aqueça o forno a 250°C com uma panela de ferro dentro.',
            //         'Coloque o pão na panela quente, faça um corte no topo.',
            //         'Asse por 30 minutos com tampa, depois retire a tampa por mais 15 minutos.',
            //     ],
            //     'prep_time' => 30,
            //     'cook_time' => 45,
            //     'servings' => 1,
            //     'difficulty' => 'hard',
            //     'category' => 'Pães',
            //     'is_published' => true,
            //     'user_id' => $chef->id,
            // ],
            // [
            //     'title' => 'Salada Caesar com Frango Grelhado',
            //     'description' => 'Salada fresca e nutritiva com molho caesar caseiro.',
            //     'ingredients' => [
            //         '1 peito de frango',
            //         '1 alface americana',
            //         '1/2 xícara de croutons',
            //         '1/4 xícara de parmesão ralado',
            //         '2 colheres de azeite',
            //         '1 colher de mostarda',
            //         '1 dente de alho',
            //         'Suco de 1 limão',
            //         'Sal e pimenta a gosto',
            //     ],
            //     'instructions' => [
            //         'Tempere o frango com sal, pimenta e azeite.',
            //         'Grelhe o frango até dourar e cozinhar completamente.',
            //         'Deixe descansar e corte em fatias.',
            //         'Para o molho: misture azeite, mostarda, alho e suco de limão.',
            //         'Lave e corte o alface em pedaços.',
            //         'Monte a salada com alface, frango, croutons e queijo.',
            //         'Regue com o molho caesar e sirva.',
            //     ],
            //     'prep_time' => 15,
            //     'cook_time' => 20,
            //     'servings' => 2,
            //     'difficulty' => 'easy',
            //     'category' => 'Saladas',
            //     'is_published' => true,
            //     'user_id' => $chef->id,
            // ],
            // [
            //     'title' => 'Brigadeiro Gourmet',
            //     'description' => 'Brigadeiro cremoso com chocolate belga, perfeito para festas.',
            //     'ingredients' => [
            //         '1 lata de leite condensado',
            //         '3 colheres de sopa de cacau em pó',
            //         '1 colher de sopa de manteiga',
            //         '100g de chocolate meio amargo',
            //         'Chocolate granulado para enrolar',
            //     ],
            //     'instructions' => [
            //         'Derreta o chocolate em banho-maria.',
            //         'Em uma panela, misture o leite condensado, cacau e manteiga.',
            //         'Adicione o chocolate derretido e mexa em fogo baixo.',
            //         'Cozinhe até desgrudar do fundo da panela.',
            //         'Deixe esfriar completamente.',
            //         'Unte as mãos com manteiga e enrole em bolinhas.',
            //         'Passe no chocolate granulado e coloque em forminhas.',
            //     ],
            //     'prep_time' => 30,
            //     'cook_time' => 15,
            //     'servings' => 30,
            //     'difficulty' => 'medium',
            //     'category' => 'Doces',
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
