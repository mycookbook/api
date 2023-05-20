<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StaticContentsSeeder extends Seeder
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
        \Illuminate\Support\Facades\DB::table('static_contents')->insert([
            [
                'title' => 'cookie-policy',
                'content' => file_get_contents(__DIR__.'/policies/cookie-policy.php'),
            ], [
                'title' => 'usage-policy',
                'content' => file_get_contents(__DIR__.'/policies/usage-policy.php'),
            ], [
                'title' => 'data-retention-policy',
                'content' => file_get_contents(__DIR__.'/policies/data-retention-policy.php'),
            ], [
                'title' => 'terms-and-conditions',
                'content' => file_get_contents(__DIR__.'/policies/terms-and-conditions.php'),
            ],
        ]);
    }
}
