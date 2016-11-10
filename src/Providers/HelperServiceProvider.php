<?php

namespace Caffeinated\Modules\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $file = $this->app->make(Filesystem::class);

        $path = realpath(__DIR__.'/../Helpers');
        $helpers = $file->glob($path.'/*.php');

        foreach ($helpers as $helper) {
            require_once $helper;
        }
    }
}
