<?php

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function createRoles()
    {
        DB::table('roles')->insert([
            [
                'role_id' => 'super',
            ], [
                'role_id' => 'contributor',
            ]
        ]);
    }

    protected function createUserRole($user_id, $role_id)
    {
        $role_id = DB::table('roles')->where(['role_id' => $role_id])->first()->id;

        $role = new Role();
        $role->user_id = $user_id;
        $role->role_id = $role_id;
        $role->save();
    }
}
