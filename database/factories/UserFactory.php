<?php

$factory->define(
    App\User::class, function (Faker\Generator $faker) {
        return [
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => app('hash')->make('secret'),
            'following' => rand(1, 100),
            'followers' => rand(1, 100),
            'name_slug' => $faker->name,
        ];
    }
);
