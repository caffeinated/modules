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
		$module = $this->argument('module');

		if (isset($module)) {
			if ($this->module->has($module)) {
				return $this->seed($module);

				$moduleName = Str::studly($module);

				return $this->info("Module [$moduleName] has been seeded.");
			}

			return $this->error("Module [$moduleName] does not exist.");
		} else {
			foreach ($this->module->all() as $module) {
				$this->seed($module['slug']);

				$moduleName = $module['name'];
				
				return $this->info("Module [$moduleName] has been seeded.");
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
		$params['--class'] = $this->option('class') ? $this->option('class') : Str::studly($module).'DatabaseSeeder';

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