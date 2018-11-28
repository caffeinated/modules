<?php

namespace Caffeinated\Modules;

use Illuminate\Support\ServiceProvider;
use Caffeinated\Modules\Contracts\Repository;
use Caffeinated\Modules\Providers\BladeServiceProvider;
use Caffeinated\Modules\Providers\ConsoleServiceProvider;
use Caffeinated\Modules\Providers\GeneratorServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * @var bool Indicates if loading of the provider is deferred.
     */
    protected $defer = false;
    
    /**
     * Bootstrap the provided services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/modules.php' => config_path('modules.php'),
        ], 'config');

        $this->app['modules']->register();
    }

    /**
     * Register the provided services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/modules.php', 'modules'
        );

        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(GeneratorServiceProvider::class);
        $this->app->register(BladeServiceProvider::class);

        $this->app->singleton('modules', function ($app) {
            return new RepositoryManager($app);
        });
    }

    /**
     * Get the services provided by the package.
     *
     * @return array
     */
    public function provides()
    {
        return ['modules'];
    }

    /**
     * Register compilable code.
     * 
     * @return array
     */
    public static function compiles()
    {
        $files = [];

        foreach (modules()->repositories() as $repository) {
            foreach ($repository->all() as $module) {
                $serviceProvider = module_class($module['slug'], 'Providers\\ModuleServiceProvider', $repository->location);

                if (class_exists($serviceProvider)) {
                    $files = array_merge($files, forward_static_call([$serviceProvider, 'compiles']));
                }
            }
        }

        return array_map('realpath', $files);
    }
}
