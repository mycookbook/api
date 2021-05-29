<?php

namespace App\Providers;

use App\Adapters\Search\FulltextSearchAdapterInterface;
use Illuminate\Support\Facades\App;
use Tymon\JWTAuth\Providers\LumenServiceProvider;
use App\Adapters\Search\MySqlAdapter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	public function boot()
	{
//		dd(App::environment());
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
