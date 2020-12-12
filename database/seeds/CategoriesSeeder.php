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
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'ketogenic',
				'slug' => 'ketogenic',
				'color' => 'a0f1f0',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'health and wellness',
				'slug' => 'health-and-wellness',
				'color' => 'ffffe0',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'cocktail and drinks',
				'slug' => 'cocktails-and-drinks',
				'color' => 'a0f1f0',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'FitFam',
				'slug' => 'fitfam',
				'color' => 'ffffe0',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			], [
				'name' => 'side dishes',
				'slug' => 'side-dishes',
				'color' => 'FFFF33',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			]
		]);
    }
}
