<?php

if (!function_exists('module_path')) {
    /**
     * Return the path to the given module file.
     *
     * @param string $module
     * @param string $file
     *
     * @return string
     */
    function module_path($module = null, $file = '')
    {
        $modulesPath = config('modules.path');
        $pathMap = config('modules.pathMap');

        if (!empty($file) && !empty($pathMap)) {
            $file = str_replace(
                array_keys($pathMap),
                array_values($pathMap),
                $file
            );
        }

        $filePath = $file ? '/'.ltrim($file, '/') : '';

        if (is_null($module)) {
            if (empty($file)) {
                return $modulesPath;
            }

            return $modulesPath.$filePath;
        }

        $module = Module::where('slug', $module);

        return $modulesPath.'/'.$module['basename'].$filePath;
    }
}

if (!function_exists('module_class')) {
    /**
     * Return the full path to the given module class.
     *
     * @param string $module
     * @param string $class
     *
     * @return string
     */
    function module_class($module, $class)
    {
        $module = Module::where('slug', $module);
        $namespace = config('modules.namespace').$module['basename'];

        return "{$namespace}\\{$class}";
    }
}
