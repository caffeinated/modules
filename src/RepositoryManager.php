<?php

namespace Caffeinated\Modules;

use Exception;
use Illuminate\Foundation\Application;
use Caffeinated\Modules\Repositories\Repository;
use Caffeinated\Modules\Exceptions\ModuleNotFoundException;

class RepositoryManager
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository[]
     */
    protected $repositories = [];

    /**
     * Create a new Modules instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function location($location = null)
    {
        return $this->repository($location);
    }

    /**
     * Register the module service provider file from all modules.
     *
     * @return void
     */
    public function register()
    {
        foreach (array_keys(config('modules.locations')) as $location) {
            $repository = $this->repository($location);
            $modules    = $repository->enabled();

            $modules->each(function ($module) use ($repository) {
                try {
                    $this->registerServiceProvider($repository, $module);

                    $this->autoloadFiles($module);
                } catch (ModuleNotFoundException $e) {
                    //
                }
            });
        }
    }

    /**
     * Register the module service provider.
     *
     * @param array $module
     *
     * @return void
     */
    private function registerServiceProvider(Repository $repository, $module)
    {
        $location        = $repository->location;
        $provider        = config("modules.location.$location.provider", 'Providers\\ModuleServiceProvider');
        $serviceProvider = module_class($module['slug'], $provider, $location);

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
     * @return \Caffeinated\Modules\Repositories\Repository[]
     */
    public function repositories()
    {
        return $this->repositories;
    }

    /**
     * @param string $location
     * @return \Caffeinated\Modules\Repositories\Repository
     * @throws \Exception
     */
    protected function repository($location = null)
    {
        $location = $location ?: config('modules.default_location');
        $driverClass = $this->repositoryClass($location);

        if (! $driverClass) {
            throw new Exception("[$location] not found. Check your module locations configuration.");
        }

        return  $this->repositories[$location]
            ?? $this->repositories[$location] = new $driverClass($location, $this->app['config'], $this->app['files']);
    }

    /**
     * @param $location
     * @return \Illuminate\Config\Repository|mixed
     * @throws \Exception
     */
    protected function repositoryClass($location)
    {
        $locationConfig = config("modules.locations.$location");

        if (is_null($locationConfig)) {
            throw new Exception("Location [$location] not configured. Please check your modules.php configuration file.");
        }

        $driver = $locationConfig['driver'] ?? config('modules.default_driver');

        return config("modules.drivers.$driver");
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
        return call_user_func_array([$this->repository(), $method], $arguments);
    }
}
