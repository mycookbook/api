<?php

namespace App\Providers;

use App\Adapters\Search\MySqlAdapter;
use Illuminate\Support\ServiceProvider;
use App\Adapters\Search\FulltextSearchAdapterInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(FulltextSearchAdapterInterface::class, MySqlAdapter::class);
    }
}
