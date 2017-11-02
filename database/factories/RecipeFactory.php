<?php

$factory->define(
    App\Recipe::class, function (Faker\Generator $faker) {
        return [
            'name' => $faker->streetName,
            'ingredients' => 'simple recipe',
            'description' => $faker->sentence(),
            'imgUrl' => $faker->imageUrl(800, 600, 'food'),
            'user_id' => rand(2, 49),
            'cookbook_id' => rand(3, 45),
        ];
    }
);
