<?php

$factory->define(
    \App\Models\Recipe::class, function (Faker\Generator $faker) {
        return [
            'name' => $faker->streetName,
            'ingredients' => 'simple recipe',
            'description' => $faker->sentence(),
            'imgUrl' => $faker->imageUrl(800, 600, 'food'),
            'user_id' => rand(1, 4),
            'cookbook_id' => rand(1, 4),
            'summary' => 'Cook pasta per package directions. Reserve 3/4 cup cooking liquid, then drain. Meanwhile,
            heat oil in a large, deep skillet on medium. Cook shallot,',
            'nutritional_detail' => 'low carbs',
            'calorie_count' => 1300,
        ];
    }
);
