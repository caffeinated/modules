<?php

namespace Caffeinated\Modules;

use Caffeinated\Modules\Contracts\Repository;
use Illuminate\Foundation\Application;

class Modules
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Create a new Modules instance.
     *
     * @param Application $app
     * @param Repository  $repository
     */
    public function __construct(Application $app, Repository $repository)
    {
        $this->app = $app;
        $this->repository = $repository;
    }

    /**
     * Register the module service provider file from all modules.
     *
     * @return void
     */
    public function register()
    {
        $modules = $this->repository->enabled();

        $modules->each(function ($module) {
            $this->registerServiceProvider($module);

            $this->autoloadFiles($module);
        });
    }

    /**
     * Register the module service provider.
     *
     * @param array $module
     *
     * @return void
     */
    private function registerServiceProvider($module)
    {
        $serviceProvider = module_class($module['slug'], 'Providers\\ModuleServiceProvider');

        if (class_exists($serviceProvider)) {
            $this->app->register($serviceProvider);
        }
    }

    /**
     * Autoload custom module files.
     *
     * @param array $module
     *
     * @return void
     */
    private function autoloadFiles($module)
    {
        if (isset($module['autoload'])) {
            foreach ($module['autoload'] as $file) {
                $path = module_path($module['slug'], $file);

                if (file_exists($path)) {
                    include $path;
                }
            }
        }
    }

    /**
     * Oh sweet sweet magical method.
     *
     * @param string $method
     * @param mixed  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->repository, $method], $arguments);
    }
}
