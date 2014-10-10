<?php

namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleSeedCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:seed';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Seed the database with records for a specific or all modules';

	/**
	 * @var Caffeinated\Modules\Modules
	 */
	protected $module;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Modules $module)
	{
		parent::__construct();

		$this->module = $module;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$module     = $this->argument('module');
		$moduleName = Str::studly($module);

		if (isset($module)) {
			if ($this->module->has($moduleName)) {
				$this->seed($module);

				return;
			}

			return $this->error("Module [$moduleName] does not exist.");
		} else {
			foreach ($this->module->all() as $module) {
				$this->seed($module['slug']);
			}
		}
	}

	/**
	 * Seed the specific module.
	 *
	 * @param string $module
	 * @return array
	 */
	protected function seed($module)
	{
		$params     = array();
		$moduleName = Str::studly($module);
		$namespace  = $this->module->getNamespace();
		$rootSeeder = $moduleName.'DatabaseSeeder';
		$fullPath   = $namespace.$moduleName.'\Database\Seeds\\'.$rootSeeder;

		if ($this->option('class')) {
			$params['--class'] = $this->option('class');
		} else {
			$params['--class'] = $fullPath;
		}

		if ($option = $this->option('database')) {
			$params['--database'] = $option;
		}

		$this->call('db:seed', $params);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [['module', InputArgument::OPTIONAL, 'Module slug.']];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the module\'s root seeder.'],
			['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed.']
		];
	}
}