<?php

declare(strict_types=1);

namespace App\Providers;

use App\Adapters\Search\FulltextSearchAdapterInterface;
use App\Adapters\Search\MySqlAdapter;
use Illuminate\Support\ServiceProvider;

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
