<?php
namespace Caffeinated\Modules\Providers;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
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
        $this->registerMakeControllerCommand();
        $this->registerMakeMigrationCommand();
        $this->registerMakeModuleCommand();
        $this->registerMakeRequestCommand();
    }

    /**
     * Register the make:module:controller command.
     *
     * @return void
     */
    private function registerMakeControllerCommand()
    {
        $this->app->singleton('command.make.module.controller', function($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeControllerCommand'];
        });

        $this->commands('command.make.module.controller');
    }

    /**
     * Register the make:module command.
     *
     * @return void
     */
    private function registerMakeMigrationCommand()
    {
        $this->app->singleton('command.make.module.migration', function($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeMigrationCommand'];
        });

        $this->commands('command.make.module.migration');
    }

    /**
     * Register the make:module command.
     *
     * @return void
     */
    private function registerMakeModuleCommand()
    {
        $this->app->singleton('command.make.module', function($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeModuleCommand'];
        });

        $this->commands('command.make.module');
    }

    /**
     * Register the make:module:request command.
     *
     * @return void
     */
    private function registerMakeRequestCommand()
    {
        $this->app->singleton('command.make.module.request', function($app) {
            return $app['Caffeinated\Modules\Console\Generators\MakeRequestCommand'];
        });

        $this->commands('command.make.module.request');
    }
}
