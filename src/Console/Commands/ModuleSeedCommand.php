<?php
namespace Caffeinated\Modules\Console\Commands;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleSeedCommand extends Command
{
	/**
	 * @var string $name The console command name.
	 */
	protected $name = 'module:seed';

	/**
	 * @var string $description The console command description.
	 */
	protected $description = 'Seed the database with records for a specific or all modules';

	/**
	 * @var \Caffeinated\Modules\Modules
	 */
	protected $module;

	/**
	 * Create a new command instance.
	 *
	 * @param \Caffeinated\Modules\Modules $module
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
		$moduleName = studly_case($module);

		if (isset($module)) {
			if (! $this->module->exists($module)) {
				return $this->error("Module [$moduleName] does not exist.");
			}

			if ($this->module->isEnabled($module)) {
				$this->seed($module);
			} elseif ($this->option('force')) {
				$this->seed($module);
			}

			return;
		} else {
			if ($this->option('force')) {
				$modules = $this->module->all();
			} else {
				$modules = $this->module->enabled();
			}

			foreach ($modules as $module) {
				$this->seed($module['slug']);
			}
		}
	}

	/**
	 * Seed the specific module.
	 *
	 * @param  string $module
	 * @return array
	 */
	protected function seed($module)
	{
		$params     = array();
		$moduleName = studly_case($module);
		$namespace  = $this->module->getNamespace();
		$rootSeeder = $moduleName.'DatabaseSeeder';
		$fullPath   = $namespace.'\\'.$moduleName.'\Database\Seeds\\'.$rootSeeder;

		if (class_exists($fullPath)) {
			if ($this->option('class')) {
				$params['--class'] = $this->option('class');
			} else {
				$params['--class'] = $fullPath;
			}

			if ($option = $this->option('database')) {
				$params['--database'] = $option;
			}

	        if ($option = $this->option('force')) {
	            $params['--force'] = $option;
	        }

			$this->call('db:seed', $params);
		}
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
			['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
		];
	}
}
