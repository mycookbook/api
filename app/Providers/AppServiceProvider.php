<?php

namespace App\Providers;

use App\Adapters\Search\FulltextSearchAdapterInterface;
use App\Adapters\Search\MySqlAdapter;
use App\Rules\SupportedImageUrlFormatsRule;
use Illuminate\Support\ServiceProvider;
use App\Rules\NutritionalDetailJsonStructureRule;

class AppServiceProvider extends ServiceProvider
{
	public function boot()
	{
		NutritionalDetailJsonStructureRule::validate();
		SupportedImageUrlFormatsRule::validate();
	}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);

        $this->app->bind(FulltextSearchAdapterInterface::class, MySqlAdapter::class);
    }
}
