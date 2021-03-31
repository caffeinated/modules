<?php

namespace Caffeinated\Modules\Console\Generators;

use Str;
use Illuminate\Console\Command;

class MakeMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module:migration
    	{slug : The slug of the module.}
    	{name : The name of the migration.}
    	{--create= : The table to be created.}
        {--table= : The table to migrate.}
    	{--location= : The modules location to create the module migration in.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module migration file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $arguments = $this->argument();
        $option = $this->option();
        $options = [];

        array_walk($option, function (&$value, $key) use (&$options) {
            $options['--' . $key] = $value;
        });

        unset($arguments['slug']);
        unset($options['--location']);

        $modulePath = module_path($this->argument('slug'), 'Database/Migrations', $this->option('location'));
        $options['--path'] = Str::replaceFirst(realpath(base_path()), '', realpath($modulePath));
        $options['--path'] = ltrim($options['--path'], '/');

        return $this->call('make:migration', array_merge($arguments, $options));
    }
}
