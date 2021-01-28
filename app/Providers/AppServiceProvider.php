<?php

namespace App\Providers;

use App\Adapters\Search\FulltextSearchAdapterInterface;
use Tymon\JWTAuth\Providers\LumenServiceProvider;
use App\Adapters\Search\MySqlAdapter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	public function boot() {}

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
