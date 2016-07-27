<?php

namespace Caffeinated\Modules\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $driver = ucfirst(config('modules.driver'));

        if ($driver == 'Custom') {
            $namespace = config('modules.custom_driver');
        } else {
            $namespace = 'Caffeinated\Modules\Repositories\\'.$driver.'Repository';
        }

        $this->app->bind('Caffeinated\Modules\Contracts\Repository', $namespace);
    }
}
