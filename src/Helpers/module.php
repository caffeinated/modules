<?php

/**
 * Return the path to the given module file.
 *
 * @param  string  $module
 * @param  string  $file
 * @return string
 */
function module_path($module, $file = '')
{
    $module = Module::where('slug', $module);

    return config('modules.path').'/'.$module['basename'].'/'.$file;
}

/**
 * Return the full path to the given module class.
 *
 * @param  string  $slug
 * @param  string  $class
 * @return string
 */
function module_class($module, $class)
{
    $module    = Module::where('slug', $module);
    $namespace = config('modules.namespace').$module['basename'];

    return "\\{$namespace}\\{$class}";
}
