<?php

$factory->define(
    App\Flag::class, function (Faker\Generator $faker) {
        return [
            'flag' => 'ng',
            'nationality' => 'Nigerian',
        ];
    }
);
