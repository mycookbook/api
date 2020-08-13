<?php

$factory->define(
    App\Cookbook::class, function (Faker\Generator $faker) {
        return [
            'name' => $faker->jobTitle,
            'description' => $faker->sentence,
            'bookCoverImg' => 'http://localhost:5001/static/ketogenic-lifestyle.jpg',
            'user_id' => 1,
            'flag_id' => 1
        ];
    }
);
