<?php

use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 * @throws Exception
	 */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('categories')->insert([
        	[
				'name' => 'vegan',
				'slug' => 'vegan',
				'color' => 'f0e1ff',
				'emoji' => '',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'ketogenic',
				'slug' => 'ketogenic',
				'color' => 'a0f1f0',
				'emoji' => '',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'health and wellness',
				'slug' => 'health-and-wellness',
				'color' => 'ffffe0',
				'emoji' => '',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'cocktail and drinks',
				'slug' => 'cocktails-and-drinks',
				'color' => 'a0f1f0',
				'emoji' => '&#x1F378;',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'FitFam',
				'slug' => 'fitfam',
				'color' => '9acd32',
				'emoji' => '&#x1F3CB;',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'side dishes',
				'slug' => 'side-dishes',
				'color' => 'FFFF33',
				'emoji' => '',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'party',
				'slug' => 'party',
				'color' => '87ceeb',
				'emoji' => '&#x1F389;',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'birthdays',
				'slug' => 'birthdays',
				'color' => 'f75394',
				'emoji' => '&#x1F389;',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'weddings',
				'slug' => 'birthdays',
				'color' => 'FFFF00',
				'emoji' => '&#x1F388',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'preggo',
				'slug' => 'preggo',
				'color' => '6a5acd',
				'emoji' => '&#x1F476;',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			]
		]);
    }
}
