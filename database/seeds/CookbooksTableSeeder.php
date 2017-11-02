<?php

use Illuminate\Database\Seeder;

/**
 * Class CookbooksTableSeeder
 */
class CookbooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Cookbook::class, 500)->create();
    }
}
