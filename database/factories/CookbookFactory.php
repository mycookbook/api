<?php

$factory->define(
    App\Cookbook::class, function (Faker\Generator $faker) {
        return [
            'name' => $faker->jobTitle,
            'description' => $faker->sentence,
            'user_id' => rand(2, 49)
        ];
    }
);