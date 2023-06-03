<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Cookbook;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function definition()
    {
        return [
            'name' => fake()->streetName,
            'ingredients' => [fake()->name, fake()->name, fake()->name],
            'description' => fake()->sentence(300),
            'imgUrl' => fake()->imageUrl,
            'user_id' => User::factory()->make()->id,
            'cookbook_id' => Cookbook::factory()->make()->id,
            'summary' => fake()->sentence(180),
            'nutritional_detail' => 'low carbs',
            'calorie_count' => 1300,
            'nationality' => 1
        ];
    }
}
