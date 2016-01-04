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
        $this->registerMakeMigrationCommand();
        $this->registerMakeModuleCommand();
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
}
