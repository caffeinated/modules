<?php

namespace Caffeinated\Modules\Console;

use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;
use Illuminate\Support\Str;
use Module;

abstract class GeneratorCommand extends LaravelGeneratorCommand
{
    /**
     * Parse the name and format according to the root namespace.
     *
     * @param string $name
     *
     * @return string
     */
    protected function qualifyClass($name)
    {
        try {
            $location = $this->option('location') ?: config('modules.default_location');
        }
        catch (\Exception $e) {
            $location = config('modules.default_location');
        }

        $rootNamespace = config("modules.locations.$location.namespace");

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        try {
            $location = $this->option('location') ?: config('modules.default_location');
        }
        catch (\Exception $e) {
            $location = config('modules.default_location');
        }

        $slug = $this->argument('slug');
        $module = Module::location($location)->where('slug', $slug);

        // take everything after the module name in the given path (ignoring case)
        $key = array_search(strtolower($module['basename']), explode('\\', strtolower($name)));

        if ($key === false) {
            $newPath = str_replace('\\', '/', $name);
        } else {
            $newPath = implode('/', array_slice(explode('\\', $name), $key + 1));
        }

        return module_path($slug, "$newPath.php", $location);
    }
}
