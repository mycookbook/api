<?php

$factory->define(
    App\Cookbook::class, function (Faker\Generator $faker) {
        $i=['ng', 'je', 'jp', 'it', 'jm'];
        return [
            'name' => $faker->jobTitle,
            'description' => $faker->sentence,
            'bookCoverImg' => 'http://localhost:5001/static/ketogenic-lifestyle.jpg',
            'user_id' => rand(1, 4),
            'flag' => $i[array_rand($i)]
        ];
    }
);
