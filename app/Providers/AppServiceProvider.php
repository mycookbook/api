<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Rules\NutritionalDetailJsonStructure;

class AppServiceProvider extends ServiceProvider
{
	public function boot()
	{
		NutritionalDetailJsonStructure::validate();
	}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);
    }
}
