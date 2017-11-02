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
        factory(App\User::class, 4)->create();
        factory(App\Cookbook::class, 2)->create();
        factory(App\Recipe::class, 1)->create();
    }
}
