<?php

use Illuminate\Database\Seeder;

class FlagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws Exception
     */
    public function run()
    {
        DB::table('flags')->insert([
            [
                'flag' => 'ng',
                'nationality' => 'Nigerian',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'us',
                'nationality' => 'American',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'ca',
                'nationality' => 'Canadian',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'ug',
                'nationality' => 'Ugandan',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'gh',
                'nationality' => 'Ghanaian',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'france',
                'nationality' => 'French',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'af',
                'nationality' => 'Afghanistan',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'ag',
                'nationality' => 'Antigua',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'ai',
                'nationality' => 'Anguilla',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'al',
                'nationality' => 'Albania',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'am',
                'nationality' => 'Armenian',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'an',
                'nationality' => 'Netherlands',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'ao',
                'nationality' => 'From Angola',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'ar',
                'nationality' => 'From Argentina',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'as',
                'nationality' => 'From American Samoa',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'at',
                'nationality' => 'From Austria',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'au',
                'nationality' => 'Australian',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'au',
                'nationality' => 'From Austria',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'ba',
                'nationality' => 'Bosnian',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'bb',
                'nationality' => 'From Barbados',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'bd',
                'nationality' => 'Bangladesh',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'be',
                'nationality' => 'Belgium',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'bf',
                'nationality' => 'From Burkina Faso',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'bg',
                'nationality' => 'Bulgarian',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'bj',
                'nationality' => 'From the Republic of Benin',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'br',
                'nationality' => 'Brazilian',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'cd',
                'nationality' => 'Congolese',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'ch',
                'nationality' => 'Switzerland',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'cm',
                'nationality' => 'Cameroonian',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'cn',
                'nationality' => 'From China',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'co',
                'nationality' => 'Colombian',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'cu',
                'nationality' => 'From Cuba',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'de',
                'nationality' => 'From Germany',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'dk',
                'nationality' => 'From Denmark',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ], [
                'flag' => 'dk',
                'nationality' => 'From Denmark',
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
        ]);
    }
}
