<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\UserIsAuthenticated;
use App\Listeners\AddFollowers;
use App\Listeners\SaveTikTokCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            \SocialiteProviders\TikTok\TikTokExtendSocialite::class.'@handle',
            \SocialiteProviders\Twitter\TwitterExtendSocialite::class.'@handle',
            \SocialiteProviders\Pinterest\PinterestExtendSocialite::class.'@handle',
            \SocialiteProviders\Instagram\InstagramExtendSocialite::class.'@handle',
        ],

        UserIsAuthenticated::class => [
            AddFollowers::class,
            SaveTikTokCode::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
