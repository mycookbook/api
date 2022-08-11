<?php

namespace App\Providers;

use App\Adapters\Search\FulltextSearchAdapterInterface;
use App\Adapters\Search\MySqlAdapter;
use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\Providers\LumenServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //		dd(env('TESTING_DB_DATABASE'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(LumenServiceProvider::class);

        $this->app->bind(FulltextSearchAdapterInterface::class, MySqlAdapter::class);
    }
}
