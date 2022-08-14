<?php

$factory->define(
    \App\Models\Flag::class, function (Faker\Generator $faker) {
        return [
            'flag' => 'ng',
            'nationality' => 'Nigerian',
        ];
    }
);
