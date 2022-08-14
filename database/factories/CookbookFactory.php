<?php

namespace Database\Factories;

use App\Models\Cookbook;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CookbookFactory extends Factory
{
    protected $model = Cookbook::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function definition()
    {
        return [
            'name' => fake()->jobTitle,
            'description' => fake()->sentence,
            'bookCoverImg' => fake()->imageUrl(),
            'user_id' => User::factory()->make()->id,
            'flag_id' => 1,
        ];
    }
}

