<?php

namespace Caffeinated\Modules\Console\Generators;

use Illuminate\Support\Str;
use Caffeinated\Modules\Console\GeneratorCommand;

class MakeModelCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module:model
    	{slug : The slug of the module.}
    	{name : The name of the model class.}
        {--migration : Create a new migration file for the model.}
    	{--location= : The modules location to create the module model class in}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module model class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Module model';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() !== false) {
            if ($this->option('migration')) {
                $table = Str::plural(Str::snake(class_basename($this->argument('name'))));

                $this->call('make:module:migration', [
                    'slug' => $this->argument('slug'),
                    'name' => "create_{$table}_table",
                    '--create' => $table,
                    '--location' => $this->option('location'),
                ]);
            }
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/model.stub';
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
        return module_class($this->argument('slug'), 'Models', $this->option('location'));
    }
}
