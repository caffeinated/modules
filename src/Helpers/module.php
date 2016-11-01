<?php

if (! function_exists('module_path')) {
    /**
     * Return the path to the given module file.
     *
     * @param  string  $module
     * @param  string  $file
     * @return string
     */
    function module_path($module = null, $file = '')
    {
        if (is_null($module)) {
            if (empty($file)) {
                return config('modules.path');
            }

            return config('modules.path').'/'.$file;
        }

        $module = Module::where('slug', $module);

        if (empty($file)) {
            return config('modules.path').'/'.$module['basename'];
        }

        return config('modules.path').'/'.$module['basename'].'/'.$file;
    }
}


if (! function_exists('module_class')) {
    /**
     * Return the full path to the given module class.
     *
     * @param  string  $module
     * @param  string  $class
     * @return string
     */
    function module_class($module, $class)
    {
        $module    = Module::where('slug', $module);
        $namespace = config('modules.namespace').$module['basename'];

        return "{$namespace}\\{$class}";
    }
}
