<?php

$factory->define(
    App\Cookbook::class, function (Faker\Generator $faker) {
        return [
            'name' => $faker->jobTitle,
            'description' => $faker->sentence,
            'bookCoverImg' => $faker->imageUrl(),
            'user_id' => 1,
            'flag_id' => 1
        ];
    }
);
