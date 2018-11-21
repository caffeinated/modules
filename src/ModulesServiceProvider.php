<?php

namespace Caffeinated\Modules;

use Caffeinated\Modules\Contracts\Repository;
use Caffeinated\Modules\Providers\BladeServiceProvider;
use Caffeinated\Modules\Providers\ConsoleServiceProvider;
use Caffeinated\Modules\Providers\GeneratorServiceProvider;
use Caffeinated\Modules\Providers\HelperServiceProvider;
use Caffeinated\Modules\Providers\RepositoryServiceProvider;
use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * @var bool Indicates if loading of the provider is deferred.
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/modules.php' => config_path('modules.php'),
        ], 'config');

        $this->app['modules']->register();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/modules.php', 'modules'
        );

        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(GeneratorServiceProvider::class);
        $this->app->register(HelperServiceProvider::class);
        $this->app->register(BladeServiceProvider::class);

        $this->app->singleton('modules', function ($app) {
            return new ModuleRepositoriesManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['modules'];
    }

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
