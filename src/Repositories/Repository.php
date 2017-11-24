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

    /*
    |--------------------------------------------------------------------------
    | Optimization Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Update cached repository of module information.
     *
     * @return bool
     */
    public function optimize()
    {

        $cache = $this->load();
        $basenames = $this->getAllBasenames();
        $modules = collect();

        $basenames->each(function ($module, $key) use ($modules, $cache) {
            $basename = collect(['basename' => $module]);
            $temp = $basename->merge(collect($cache->get($module)));
            $manifest = $temp->merge(collect($this->getManifest($module)));

            $modules->put($module, $manifest);
        });

        $modules->each(function ($module) {
            $module->put('id', crc32($module->get('slug')));

            if (!$module->has('initialized')) {
                $module->put('initialized', config('modules.initialized', true));
            }

            if (!$module->has('enabled')) {
                $module->put('enabled', config('modules.enabled', true));
            }

            if (!$module->has('order')) {
                $module->put('order', 9001);
            }

            return $module;
        });

        $content = json_encode($modules->all(), JSON_PRETTY_PRINT);

        return $this->save($content);
    }

    /*
    |--------------------------------------------------------------------------
    | Collection Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Get all modules.
     *
     * @return Collection
     */
    public function all()
    {
        return $this->load()->sortBy('order');
    }

    /**
     * Get all module slugs.
     *
     * @return Collection
     */
    public function slugs()
    {
        $slugs = collect();

        $this->all()->each(function ($item, $key) use ($slugs) {
            $slugs->push(strtolower($item['slug']));
        });

        return $slugs;
    }

    /**
     * Get modules based on where clause.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Collection
     */
    public function where($key, $value)
    {
        return collect($this->all()->where($key, $value)->first());
    }

    /**
     * Sort modules by given key in ascending order.
     *
     * @param string $key
     *
     * @return Collection
     */
    public function sortBy($key)
    {
        $collection = $this->all();

        return $collection->sortBy($key);
    }

    /**
     * Sort modules by given key in ascending order.
     *
     * @param string $key
     *
     * @return Collection
     */
    public function sortByDesc($key)
    {
        $collection = $this->all();

        return $collection->sortByDesc($key);
    }

    /**
     * Determines if the given module exists.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function exists($slug)
    {
        return $this->slugs()->contains($slug);
    }

    /**
     * Returns count of all modules.
     *
     * @return int
     */
    public function count()
    {
        return $this->all()->count();
    }

    /**
     * Get all module basenames.
     *
     * @return array
     */
    public function getAllBasenames()
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

    /*
    |--------------------------------------------------------------------------
    | Manifests and their paths Methods
    |--------------------------------------------------------------------------
    |
    */

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
    protected function getPath()
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
    protected function setPath($path)
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
    protected function getModulePath($slug)
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

    /*
    |--------------------------------------------------------------------------
    | Repository read and write Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Get a module property value.
     *
     * @param string $property
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($property, $default = null)
    {
        list($slug, $key) = explode('::', $property);

        $module = $this->where('slug', $slug);

        return $module->get($key, $default);
    }

    /**
     * Set the given module property value.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return bool
     */
    public function set($property, $value)
    {
        list($slug, $key) = explode('::', $property);

        $cache = $this->load();
        $module = $this->where('slug', $slug);

        if (isset($module[$key])) {
            unset($module[$key]);
        }

        $module[$key] = $value;

        $module = collect([$module['basename'] => $module]);

        $merged = $cache->merge($module);
        $content = json_encode($merged->all(), JSON_PRETTY_PRINT);

        return $this->save($content);
    }


    /*
    |--------------------------------------------------------------------------
    | Initialization Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Get all initialized modules.
     *
     * @return Collection
     */
    public function initialized()
    {
        return $this->all()->where('initialized', true);
    }

    /**
     * Get all uninitialized modules.
     *
     * @return Collection
     */
    public function uninitialized()
    {
        return $this->all()->where('initialized', false);
    }

    /**
     * Check if specified module is initialized.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isInitialized($slug)
    {
        $module = $this->where('slug', $slug);

        return $module['initialized'] === true;
    }

    /**
     * Check if specified module is uninitialized.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isUninitialized($slug)
    {
        $module = $this->where('slug', $slug);

        return $module['initialized'] === false;
    }

    /**
     * Initialize the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function initialize($slug)
    {
        if ($this->callMaintenanceMethod($slug, 'initialize')) {
            return $this->set($slug.'::initialized', true);
        } else {
            return false;
        }
    }

    /**
     * Uninitialize the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function uninitialize($slug)
    {
        if ($this->callMaintenanceMethod($slug, 'uninitialize')) {
            return $this->set($slug.'::initialized', false);
        } else {
            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Enabling Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Get all enabled modules.
     *
     * @return Collection
     */
    public function enabled()
    {
        return $this->all()->where('enabled', true);
    }

    /**
     * Get all disabled modules.
     *
     * @return Collection
     */
    public function disabled()
    {
        return $this->all()->where('enabled', false);
    }

    /**
     * Check if specified module is enabled.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isEnabled($slug)
    {
        $module = $this->where('slug', $slug);

        return $module['enabled'] === true;
    }

    /**
     * Check if specified module is disabled.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isDisabled($slug)
    {
        $module = $this->where('slug', $slug);

        return $module['enabled'] === false;
    }

    /**
     * Enables the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function enable($slug)
    {
        if ($this->callMaintenanceMethod($slug, 'enable')) {
            return $this->set($slug.'::enabled', true);
        } else {
            return false;
        }
    }

    /**
     * Disables the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function disable($slug)
    {
        if ($this->callMaintenanceMethod($slug, 'disable')) {
            return $this->set($slug.'::enabled', false);
        } else {
            return false;
        }
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
