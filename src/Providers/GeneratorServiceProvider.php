<?php

namespace Caffeinated\Modules\Providers;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
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
        $this->registerMakeControllerCommand();
        $this->registerMakeMiddlewareCommand();
        $this->registerMakeMigrationCommand();
        $this->registerMakeModelCommand();
        $this->registerMakeModuleCommand();
        $this->registerMakeRequestCommand();
        $this->registerMakeSeederCommand();
    }

    /**
     * Register the make:module:controller command.
     */
    private function registerMakeControllerCommand()
    {
        $this->app->singleton('command.make.module.controller', function ($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeControllerCommand'];
        });

        $this->commands('command.make.module.controller');
    }

    /**
     * Register the make:module:middleware command.
     */
    private function registerMakeMiddlewareCommand()
    {
        $this->app->singleton('command.make.module.middleware', function ($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeMiddlewareCommand'];
        });

        $this->commands('command.make.module.middleware');
    }

    /**
     * Register the make:module:migration command.
     */
    private function registerMakeMigrationCommand()
    {
        $this->app->singleton('command.make.module.migration', function ($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeMigrationCommand'];
        });

        $this->commands('command.make.module.migration');
    }

    /**
     * Register the make:module:model command.
     */
    private function registerMakeModelCommand()
    {
        $this->app->singleton('command.make.module.model', function ($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeModelCommand'];
        });

        $this->commands('command.make.module.model');
    }

    /**
     * Register the make:module command.
     */
    private function registerMakeModuleCommand()
    {
        $this->app->singleton('command.make.module', function ($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeModuleCommand'];
        });

        $this->commands('command.make.module');
    }

    /**
     * Register the make:module:request command.
     */
    private function registerMakeRequestCommand()
    {
        $this->app->singleton('command.make.module.request', function ($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeRequestCommand'];
        });

        $this->commands('command.make.module.request');
    }

    /**
     * Register the make:module:seeder command.
     */
    private function registerMakeSeederCommand()
    {
        $this->app->singleton('command.make.module.seeder', function ($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeSeederCommand'];
        });

        $this->commands('command.make.module.seeder');
    }
}
