<?php
namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMigrateCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:migrate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run the database migrations for a specific or all modules';

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
		$slug = $this->argument('module');

		if (isset($slug)) {
			return $this->migrate($slug);
		} else {
			foreach ($this->module->all() as $module) {
				$this->migrate($module['slug']);
			}
		}
	}

	/**
	 * Run migrations for the specified module.
	 *
	 * @param string $slug
	 * @return mixed
	 */
	protected function migrate($slug)
	{
		$moduleName = Str::studly($slug);

		if ($this->module->has($moduleName)) {
			$params = $this->getParameters($slug);

			return $this->call('migrate', $params);
		}
		
		return $this->error("Module [$moduleName] does not exist.");
	}

	/**
	 * Get migration directory path.
	 *
	 * @param string $slug
	 * @return string
	 */
	protected function getMigrationPath($slug)
	{
		$path = str_replace(base_path(), '', $this->module->getModulePath($slug));

		return $path.'/Database/Migrations/';
	}

	/**
	 * Get the console command parameters.
	 *
	 * @param string $slug
	 * @return array
	 */
	protected function getParameters($slug)
	{
		$params = [];

		$params['--path'] = $this->getMigrationPath($slug);

		if ($option = $this->option('database')) {
			$params['--database'] = $option;
		}

		if ($option = $this->option('pretend')) {
			$params['--pretend'] = $option;
		}

		if ($option = $this->option('seed')) {
			$params['--seed'] = $option;
		}

		return $params;
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
			['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
			['pretend', null, InputOption::VALUE_OPTIONAL, 'Dump the SQL queries that would be run.'],
			['seed', null, InputOption::VALUE_OPTIONAL, 'Indicates if the seed task should be re-run.']
		];
	}
}