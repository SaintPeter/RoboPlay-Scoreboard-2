<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Setup Morph Map for Yearables
	    Relation::morphMap([
			'Competition' => \App\Models\Competition::class,
			'Division' => \App\Models\Division::class,
			'Vid_competition' => \App\Models\Vid_competition::class,
			'Vid_division' => \App\Models\Vid_division::class,
		    'Video' => \App\Models\Video::class,
		    'Team' => \App\Models\Team::class
	    ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // IDE Helper is only for development only
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
