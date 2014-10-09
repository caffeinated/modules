<?php

namespace Caffeinated\Modules\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMigrateRefreshCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:migrate-refresh';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reset and re-run all migrations for a specific or all modules';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
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
		$database   = $this->option('database');

		$this->call('module:migrate-reset', [
			'module'     => $module,
			'--database' => $database
		]);

		$this->call('module:migrate', [
			'module'     => $module,
			'--database' => $database
		]);

		if ($this->needsSeeding()) {
			$this->runSeeder($module, $database);
		}

		if (isset($module)) {
			$this->info("Module [$moduleName] has been refreshed.");
		} else {
			$this->info("All modules have been refreshed.");
		}
		
	}

	/**
	 * Determine if the developer has requested database seeding.
	 *
	 * @return bool
	 */
	protected function needsSeeding()
	{
		return $this->option('seed');
	}

	/**
	 * Run the module seeder command.
	 *
	 * @param string $database
	 * @return void
	 */
	protected function runSeeder($module = null, $database = null)
	{
		$this->call('module:seed', [
			'module'     => $this->argument('module'),
			'--database' => $database
		]);
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
			['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.']
		];
	}
}