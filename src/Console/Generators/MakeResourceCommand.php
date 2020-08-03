<?php

namespace Caffeinated\Modules\Console\Generators;

use Caffeinated\Modules\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeResourceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module:resource
    	{slug : The slug of the module.}
    	{name : The name of the HTTP resource class.}
    	{--c|collection : Create a resource collection}
    	{--location= : The modules location to create the module HTTP resource class in}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module HTTP resource class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Module HTTP resource';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->collection()
            ? __DIR__ . '/stubs/resource-collection.stub'
            : __DIR__ . '/stubs/resource.stub';
    }

    /**
     * Determine if the command is generating a resource collection.
     *
     * @return bool
     */
    protected function collection()
    {
        return $this->option('collection') ||
            Str::endsWith($this->argument('name'), 'Collection');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return module_class($this->argument('slug'), 'Http\\Resources', $this->option('location'));
    }
}
