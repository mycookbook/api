<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DefinitionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('definitions')->insert([
			[
				'label' => 'categories',
				'contents' => json_encode([
					[
						"name" => "Vegan",
						"slug" => "vegan",
						"color" => "f0e1ff"
					], [
						"name" => "Ketogenic",
						"slug" => "ketogenic",
						"color" => "a0f1f0"
					], [
						"name" => "Heath & Wellness",
						"slug" => "health-and-wellness",
						"color" => "ffffe0"
					], [
						"name" => "Cocktails & Drinks",
						"slug" => "cocktails-and-drinks",
						"color" => "a0f1f0"
					], [
						"name" => "FitFam",
						"slug" => "fitfam",
						"color" => "ffffe0"
					], [
						"name" => "Side Dishes",
						"slug" => "side-dishes",
						"color" => "FFFF33"
					]
				])
			], [
				'label' => 'nutritional_details',
				'contents' => json_encode([
					[
						"item" => "cal",
						"unit" => "kcal"
					], [
						"item" => "carbs",
						"unit" => "g"
					], [
						"item" => "protein",
						"unit" => "g"
					], [
						"item" => "fat",
						"unit" => "g"
					]
				])
			], [
				'label' => 'pronouns',
				'contents' => json_encode([
					'He/Him', 'She/Her', 'They/Them'
				])
			], [
				'label' => 'expertise',
				'contents' => json_encode([
					'hobbyist',
					'foodie',
					'professional chef',
					'chef',
					'student',
					'bartender',
					'experimentalist'
				])
			]
		]);
    }
}
