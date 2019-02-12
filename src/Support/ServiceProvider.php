<?php

namespace Caffeinated\Modules\Support;

use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // Intentionally left blank.
    }

    /**
     * Register any additional module middleware.
     *
     * @param array|string $middleware
     *
     * @return void
     */
    protected function addMiddleware($middleware)
    {
        $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];

        if (is_array($middleware)) {
            foreach ($middleware as $ware) {
                $kernel->pushMiddleware($ware);
            }
        } else {
            $kernel->pushMiddleware($middleware);
        }
    }

    /**
     * Register any additional config.
     *
     * @param string $path
     *
     * @return void
     */
    protected function loadConfigsFrom($path)
    {
        if (! $this->app->configurationIsCached()) {
            $files = $this->getConfigurationFiles($path);

            foreach ($files as $key => $path) {
                config()->set($key, require $path);
            }
        }
    }

    /**
     * Load all module factories
     *
     * @param string $path
     *
     * @return void
     */
    protected function loadFactoriesFrom($path)
    {
        app(Factory::class)->load($path);
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  string  $path
     * @return array
     */
    private function getConfigurationFiles($path) {
        $files      = [];
        $configPath = realpath($path);

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $configPath
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, $configPath)
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested).'.';
        }
        
        return $nested;
    }
}
