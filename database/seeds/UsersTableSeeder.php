<?php

use Illuminate\Database\Seeder;

/**
 * Class Users Table Seeder
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Category::class, 1)->create();
        factory(App\Flag::class, 1)->create();
        factory(App\User::class, 4)->create();
        factory(App\Cookbook::class, 8)->create();
        factory(App\Recipe::class, 4)->create();
    }
}
