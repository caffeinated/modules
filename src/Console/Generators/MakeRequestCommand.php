<?php

namespace Caffeinated\Modules\Console\Generators;

use Caffeinated\Modules\Console\GeneratorCommand;

class MakeRequestCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module:request
    	{slug : The slug of the module.}
    	{name : The name of the form request class.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module form request class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Module request';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/request.stub';
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
        return module_class($this->argument('slug'), 'Http\\Requests');
    }
}
