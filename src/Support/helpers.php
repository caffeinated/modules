<?php

use Caffeinated\Modules\Exceptions\ModuleNotFoundException;

if (!function_exists('modules')) {
    /**
     * Get modules repository.
     *
     * @param string $location
     * @return \Caffeinated\Modules\RepositoryManager|\Caffeinated\Modules\Repositories\Repository
     */
    function modules($location = null) {
        if ($location) {
            return app('modules')->location($location);
        }

        return app('modules');
    }
}

if (!function_exists('module_path')) {
    /**
     * Return the path to the given module file.
     *
     * @param string $slug
     * @param string $file
     *
     * @param null $location
     * @return string
     * @throws \Caffeinated\Modules\Exceptions\ModuleNotFoundException
     */
    function module_path($slug = null, $file = '', $location = null)
    {
        $location = $location ?: config('modules.default_location');
        $modulesPath = config("modules.locations.$location.path");
        $mapping = config("modules.locations.$location.mapping");

        $filePath = $file ? '/' . ltrim($file, '/') : '';

        if (is_null($slug)) {
            if (empty($file)) {
                return $modulesPath;
            }

            return $modulesPath . $filePath;
        }

        $module = Module::location($location)->where('slug', $slug);

        if (is_null($module)) {
            throw new ModuleNotFoundException($slug);
        }

        return $modulesPath . '/' . $module['basename'] . $filePath;
    }
}

if (!function_exists('module_class')) {
    /**
     * Return the full path to the given module class.
     *
     * @param string $slug
     * @param string $class
     * @param string $location
     * @return string
     * @throws \Caffeinated\Modules\Exceptions\ModuleNotFoundException
     */
    function module_class($slug, $class, $location = null)
    {
        $location = $location ?: config('modules.default_location');
        $module = modules($location)->where('slug', $slug);

        if (is_null($module)) {
            throw new ModuleNotFoundException($slug);
        }

        $namespace = config("modules.locations.$location.namespace") . $module['basename'];

        return "$namespace\\$class";
    }
}
