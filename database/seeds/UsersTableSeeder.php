<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
//        DB::table('users')
//            ->insert(
//                [
//                    'name' => str_random(10),
//                    'email' => str_random(10).'@gmail.com',
//                    'password' => app('hash')->make('secret'),
//                    'following' => 1,
//                    'followers' => 1,
//                    'created_at' => date("Y-m-d H:i:s"),
//                    'updated_at' => date("Y-m-d H:i:s")
//                ]
//            );

        DB::table('recipes')
            ->insert(
                [
                    'name' => str_random(10),
                    'description' => str_random(50),
                    'ingredients' => str_random(8),
                    'imgUrl' => 'http://via.placeholder.com/350x150',
                    'user_id' => 2,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]
            );

        DB::table('cookbooks')
            ->insert(
                [
                    'name' => str_random(10),
                    'description' => str_random(50),
                    'user_id' => 2,
                    'recipe_id' => 2,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]
            );
    }
}
