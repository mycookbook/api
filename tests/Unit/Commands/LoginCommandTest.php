<?php

declare(strict_types=1);

namespace Unit\Commands;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class LoginCommandTest extends \TestCase
{
    public function test_creates_token_when_test_user_not_cached(): void
    {
        $this->artisan('auth:token')
            ->expectsOutput('Loading User from Cache ...')
            ->expectsOutput('===========================')
            ->expectsOutput('User not found in Cache, creating new User ...')
            ->expectsOutput('==============================================')
            ->expectsOutput('====================================')
            ->expectsOutput('Here you go! Use this token to access protected resources.!')
            ->assertExitCode(0);
    }

    public function test_creates_token_when_test_user_is_cached(): void
    {
        $user = User::factory()->make();
        $user->save();

        Cache::put('testUser', $user->refresh());
        $this->artisan('auth:token')
            ->expectsOutput('Loading User from Cache ...')
            ->expectsOutput('===========================')
            ->expectsOutput('====================================')
            ->expectsOutput('Here you go! Use this token to access protected resources.!')
            ->assertExitCode(0);
    }
}
