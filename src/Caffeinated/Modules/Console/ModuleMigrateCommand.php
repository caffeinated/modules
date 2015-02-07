<?php
namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMigrateCommand extends Command
{
	/**
	 * @var string $name The console command name.
	 */
	protected $name = 'module:migrate';

	/**
	 * @var string $description The console command description.
	 */
	protected $description = 'Run the database migrations for a specific or all modules';

	/**
	 * @var \Caffeinated\Modules\Modules
	 */
	protected $module;

	/**
	 * @var \Illuminate\Database\Migrations\Migrator $migrator The migrator instance.
	 */
	protected $migrator;

	/**
	 * Create a new command instance.
	 *
	 * @param \Illuminate\Database\Migrations\Migrator $migrator
	 * @param \Caffeinated\Modules\Modules             $module
	 */
	public function __construct(Migrator $migrator, Modules $module)
	{
		parent::__construct();

		$this->migrator = $migrator;
		$this->module   = $module;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->prepareDatabase();

		$module = $this->argument('module');

		if (isset($module)) {
			return $this->migrate($module);
		} else {
			foreach ($this->module->all() as $module) {
				$this->migrate($module['slug']);
			}
		}
	}

	/**
	 * Run migrations for the specified module.
	 *
	 * @param  string $slug
	 * @return mixed
	 */
	protected function migrate($slug)
	{
		$moduleName = Str::studly($slug);

		if ($this->module->exists($moduleName)) {
			$pretend = $this->option('pretend');
			$path    = $this->getMigrationPath($slug);

			$this->migrator->run($path, $pretend);

			// Once the migrator has run we will grab the note output and send it out to
			// the console screen, since the migrator itself functions without having
			// any instances of the OutputInterface contract passed into the class.
			foreach ($this->migrator->getNotes() as $note)
			{
				$this->output->writeln($note);
			}

			// Finally, if the "seed" option has been given, we will re-run the database
			// seed task to re-populate the database, which is convenient when adding
			// a migration and a seed at the same time, as it is only this command.
			if ($this->option('seed'))
			{
				$this->call('module:seed', ['module' => $slug, '--force']);
			}
		} else {
			return $this->error("Module [$moduleName] does not exist.");
		}		
	}

	/**
	 * Get migration directory path.
	 *
	 * @param  string $slug
	 * @return string
	 */
	protected function getMigrationPath($slug)
	{
		$path = $this->module->getModulePath($slug);

		return $path.'Database/Migrations/';
	}

	/**
	 * Prepare the migration database for running.
	 *
	 * @return void
	 */
	protected function prepareDatabase()
	{
		$this->migrator->setConnection($this->option('database'));

		if ( ! $this->migrator->repositoryExists())
		{
			$options = array('--database' => $this->option('database'));

			$this->call('migrate:install', $options);
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
			['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
			
			['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
			
			['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
			
			['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
		];
	}
}
