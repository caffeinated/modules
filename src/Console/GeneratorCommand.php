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
    protected function parseName($name)
    {
        $rootNamespace = config('modules.namespace');

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        if (Str::contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->parseName($this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name);
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
        $slug = $this->argument('slug');

        // take everything after the module name in the given path (ignoring case)
        $key = array_search(strtolower($slug), explode('\\', strtolower($name)));
        if ($key === false) {
            $newPath = str_replace('\\', '/', $name);
        } else {
            $newPath = implode('/', array_slice(explode('\\', $name), $key + 1));
        }

        return module_path($slug, "$newPath.php");
    }
}
