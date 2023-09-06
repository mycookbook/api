<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LoginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user/token or generate new token for given user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(UserService $userService, AuthService $authService)
    {
        $this->line('Loading User from Cache ...');
        $this->line('===========================');

        $fromCache = Cache::get('testUser');

        $email = Str::random(5) . '@console.com';

        if (!$fromCache) {
            $this->line('User not found in Cache, creating new User ...');
            $this->line('==============================================');

            $response = $userService->store(new Request([
                'name' => 'test user',
                'email' => $email,
                'password' => 'testing123'
            ]));

            $user = User::where('email', '=', $email)->first();

            Cache::put('testUser', $user);
        }

        $fromCache = Cache::get('testUser');

        $token = $authService->login(new Request([
            'email' => $fromCache["email"],
            'password' => 'testing123'
        ]));

        $this->info($token);

        $this->line('====================================');
        $this->info("Here you go! Use this token to access protected resources.!");

        return 0;
    }
}
