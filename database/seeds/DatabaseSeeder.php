<?php

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DefinitionsSeeder::class);
        $this->call(FlagsSeeder::class);


//		$cookbook = factory(App\Cookbook::class, 1)->create();
//		$cookbook->users()->attach();
//		$cookbook->users()->attach();
//		factory(App\User::class, 4)->create();
//		factory(App\Recipe::class, 4)->create();
    }
}
