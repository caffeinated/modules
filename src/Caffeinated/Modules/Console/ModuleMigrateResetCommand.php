<?php
namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Modules;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMigrateResetCommand extends Command
{
	/**
	 * @var string $name The console command name.
	 */
	protected $name = 'module:migrate-reset';

	/**
	 * @var string $description The console command description.
	 */
	protected $description = 'Rollback all database migrations for a specific or all modules';

	/**
	 * @var \Caffeinated\Modules\Modules
	 */
	protected $module;

	/**
	 * @var \Illuminate\Database\Migrations\Migrator
	 */
	protected $migrator;

	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Create a new command instance.
	 *
	 * @param \Caffeinated\Modules\Modules             $module
	 * @param \Illuminate\Filesystem\Filesystem        $files
	 * @param \Illuminate\Database\Migrations\Migrator $migrator
	 */
	public function __construct(Modules $module, Filesystem $files, Migrator $migrator)
	{
		parent::__construct();

		$this->module   = $module;
		$this->files    = $files;
		$this->migrator = $migrator;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$module = $this->argument('module');

		if ($module) {
			return $this->reset($module);
		} else {
			foreach ($this->module->all() as $module) {
				$this->reset($module['slug']);
			}
		}
	}

	/**
	 * Run the migration reset for the specified module.
	 *
	 * @param  string $slug
	 * @return mixed
	 */
	protected function reset($slug)
	{
		$this->migrator->setconnection($this->input->getOption('database'));

		$pretend = $this->input->getOption('pretend');

		$migrationPath = $this->getMigrationPath($slug);

		$migrations = $this->migrator->getMigrationFiles($migrationPath);

		if (count($migrations) == 0) {
			return $this->error('Nothing to rollback.');
		}

		// We need to reverse these migrations so that theya re "downed" in reverse
		// to what they run on "up". It lets us backtrack through the migrations
		// and properly reverse the entire database schema operation that originally
		// ran.
		foreach ($migrations as $migration) {
			$this->info('Migration: '.$migration);
			$this->runDown($slug, $migration, $pretend);
		}
	}

	/**
	 * Run "down" a migration instance.
	 *
	 * @param  string $slug
	 * @param  object $migration
	 * @param  bool   $pretend
	 * @return void
	 */
	protected function runDown($slug, $migration, $pretend)
	{
		$migrationPath = $this->getMigrationPath($slug);
		$file          = (string) $migrationPath.'/'.$migration.'.php';
		$classFile     = implode('_', array_slice(explode('_', str_replace('.php', '', $file)), 4));
		$class         = studly_case($classFile);
		$table         = $this->laravel['config']['database.migrations'];

		include ($file);

		$instance = new $class;

		$instance->down();
		$this->laravel['db']->table($table)
			->where('migration', $migration)
			->delete();
	}

	/**
	 * Get the console command parameters.
	 *
	 * @param  string $slug
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
	 * Get migrations path.
	 *
	 * @return string
	 */
	protected function getMigrationPath($slug)
	{
		$path = $this->module->getModulePath($slug).'Database/Migrations';

		return $path;
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
