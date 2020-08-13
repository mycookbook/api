<?php

use Illuminate\Database\Seeder;

class FlagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('flags')->insert([
			[
				'flag' => 'ng',
				'nationality' => 'Nigerian',
			], [
				'flag' => 'us',
				'nationality' => 'American',
			], [
				'flag' => 'ca',
				'nationality' => 'Canadian',
			], [
				'flag' => 'ug',
				'nationality' => 'Ugandan',
			], [
				'flag' => 'gh',
				'nationality' => 'Ghanian',
			], [
				'flag' => 'france',
				'nationality' => 'French',
			], [
				'flag' => 'af',
				'nationality' => 'Afghanistan',
			], [
				'flag' => 'ag',
				'nationality' => 'Antigua',
			], [
				'flag' => 'ai',
				'nationality' => 'Anguilla',
			], [
				'flag' => 'al',
				'nationality' => 'Albania',
			], [
				'flag' => 'am',
				'nationality' => 'Armenian',
			], [
				'flag' => 'an',
				'nationality' => 'Netherlands',
			], [
				'flag' => 'ao',
				'nationality' => 'From Angola',
			], [
				'flag' => 'ar',
				'nationality' => 'From Argentina',
			], [
				'flag' => 'as',
				'nationality' => 'From American Samoa',
			], [
				'flag' => 'at',
				'nationality' => 'From Austria',
			], [
				'flag' => 'au',
				'nationality' => 'Australian',
			], [
				'flag' => 'au',
				'nationality' => 'From Austria',
			], [
				'flag' => 'ba',
				'nationality' => 'Bosnian',
			], [
				'flag' => 'bb',
				'nationality' => 'From Barbados',
			], [
				'flag' => 'bd',
				'nationality' => 'Bangladesh',
			], [
				'flag' => 'be',
				'nationality' => 'Belgium',
			], [
				'flag' => 'bf',
				'nationality' => 'From Burkina Faso',
			], [
				'flag' => 'bg',
				'nationality' => 'Bulgarian',
			], [
				'flag' => 'bj',
				'nationality' => 'From the Republic of Benin',
			], [
				'flag' => 'br',
				'nationality' => 'Brazilian',
			], [
				'flag' => 'cd',
				'nationality' => 'Congolese',
			], [
				'flag' => 'ch',
				'nationality' => 'Switzerland',
			], [
				'flag' => 'cm',
				'nationality' => 'Cameroonian',
			], [
				'flag' => 'cn',
				'nationality' => 'From China',
			], [
				'flag' => 'co',
				'nationality' => 'Colombian',
			], [
				'flag' => 'cu',
				'nationality' => 'From Cuba',
			], [
				'flag' => 'de',
				'nationality' => 'From Germany',
			], [
				'flag' => 'dk',
				'nationality' => 'From Denmark',
			], [
				'flag' => 'dk',
				'nationality' => 'From Denmark',
			]
		]);
    }
}
