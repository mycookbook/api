<?php

$factory->define(
    App\Category::class, function (Faker\Generator $faker) {
        return [
            'name' => 'Vegan',
            'slug' => 'vegan',
            'color' => 'f0e1ff',
            'emoji' => '',
        ];
    }
);
