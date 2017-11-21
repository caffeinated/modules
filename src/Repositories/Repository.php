<?php

namespace Caffeinated\Modules\Repositories;

use Exception;
use Caffeinated\Modules\Contracts\Repository as RepositoryContract;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Module;

abstract class Repository implements RepositoryContract
{
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var string Path to the defined modules directory
     */
    protected $path;

    /**
     * Constructor method.
     *
     * @param \Illuminate\Config\Repository     $config
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Config $config, Filesystem $files)
    {
        $this->config = $config;
        $this->files = $files;
    }

    /**
     * Get all module basenames.
     *
     * @return array
     */
    protected function getAllBasenames()
    {
        $path = $this->getPath();

        try {
            $collection = collect($this->files->directories($path));

            $basenames = $collection->map(function ($item, $key) {
                return basename($item);
            });

            return $basenames;
        } catch (\InvalidArgumentException $e) {
            return collect([]);
        }
    }

    /**
     * Get a module's manifest contents.
     *
     * @param string $slug
     *
     * @return Collection|null
     */
    public function getManifest($slug)
    {
        if (! is_null($slug)) {
            $path     = $this->getManifestPath($slug);
            $contents = $this->files->get($path);
            $validate = @json_decode($contents, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $collection = collect(json_decode($contents, true));

                return $collection;
            }

            throw new Exception('['.$slug.'] Your JSON manifest file was not properly formatted. Check for formatting issues and try again.');
        }
    }

    /**
     * Get modules path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path ?: $this->config->get('modules.path');
    }

    /**
     * Set modules path in "RunTime" mode.
     *
     * @param string $path
     *
     * @return object $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path for the specified module.
     *
     * @param string $slug
     *
     * @return string
     */
    public function getModulePath($slug)
    {
        $module = studly_case(str_slug($slug));

        if (\File::exists($this->getPath()."/{$module}/")) {
            return $this->getPath()."/{$module}/";
        }

        return $this->getPath()."/{$slug}/";
    }

    /**
     * Get path of module manifest file.
     *
     * @param $slug
     *
     * @return string
     */
    protected function getManifestPath($slug)
    {
        return $this->getModulePath($slug).'module.json';
    }

    /**
     * Get modules namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return rtrim($this->config->get('modules.namespace'), '/\\');
    }

    /**
     * Calls the initialize maintenance method for the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function initialize($slug)
    {
        return $this->callMaintenanceMethod($slug, 'initialize');
    }

    /**
     * Calls the uninitialize maintenance method for the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function uninitialize($slug)
    {
        return $this->callMaintenanceMethod($slug, 'uninitialize');
    }

    /**
     * Calls the enable maintenance method for the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function enable($slug)
    {
        return $this->callMaintenanceMethod($slug, 'enable');
    }

    /**
     * Calls the disable maintenance method for the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function disable($slug)
    {
        return $this->callMaintenanceMethod($slug, 'disable');
    }

    /**
     * Resolve and call the requested maintenance method for the specified module.
     *
     * @param $slug
     * @param $method
     * @return bool
     */
    private function callMaintenanceMethod($slug, $method)
    {
        $rc = true;
        $moduleDef = Module::where('slug', $slug);
        $moduleMaintenaceClass = module_class($slug, 'Utils\\'.$moduleDef['basename'].'Maintenance');
        $moduleMaintenaceClassFile = module_path($slug, 'Utils/'.$moduleDef['basename'].'Maintenance.php');

        if (file_exists($moduleMaintenaceClassFile)) {
            include $moduleMaintenaceClassFile;
            $moduleMaintenace = new $moduleMaintenaceClass($slug);

            $rc = $moduleMaintenace->$method();
        }

        return $rc;
    }

}
