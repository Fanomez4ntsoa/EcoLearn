<?php

namespace App\Providers;

use App\EcoLearn\Providers\UserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the services
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make('hash')->extend('ecoLearn', function() {
            return new \App\EcoLearn\Libraries\PasswordHasher();
        });

        Auth::provider('ecoLearn', function ($app, $config) {
            return $app->make(UserProvider::class, $config);
        });

    }
}