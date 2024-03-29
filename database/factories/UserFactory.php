<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Hashing\BcryptHasher;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function definition($attributes = [])
    {
        return [
            'name' => $name = fake()->name,
            'email' => fake()->unique()->safeEmail(),
            'password' => (new BcryptHasher)->make('saltyL@k3'),
            'following' => rand(1, 100),
            'followers' => rand(1, 100),
            'name_slug' => str_replace(" ", "-", strtolower($name)),
        ];
    }
}
