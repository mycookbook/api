<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function definition()
    {
        return [
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => app('hash')->make('secret'),
            'following' => rand(1, 100),
            'followers' => rand(1, 100),
            'name_slug' => fake()->name,
        ];
    }
}
