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
        $this->registerMakeModuleCommand();
    }

    /**
     * Register the module:make command.
     *
     * @return void
     */
    private function registerMakeModuleCommand()
    {
        $this->app->singleton('command.module.make', function($app) {
            return $app['Caffeinated\Modules\Commands\ModuleMakeCommand'];
        });

        $this->commands('command.module.make');
    }
}
