<?php

namespace Skygdi\DeviceToken;

use Illuminate\Support\ServiceProvider;

class DeviceTokenServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        include __DIR__.'/routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        // register our controller
        $this->app->make('Skygdi\DeviceToken\DeviceTokenController');
    }
}
