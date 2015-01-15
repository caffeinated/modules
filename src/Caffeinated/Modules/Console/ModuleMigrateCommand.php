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
	 * The migrator instance.
	 *
	 * @var \Illuminate\Database\Migrations\Migrator
	 */
	protected $migrator;

	/**
	 * Create a new command instance.
	 *
	 * @return void
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
	 * @param string $slug
	 * @return mixed
	 */
	protected function migrate($slug)
	{
		$moduleName = Str::studly($slug);

		if ($this->module->exists($moduleName)) {
			$pretend = $this->input->getOption('pretend');
			$path    = $this->getMigrationPath($slug);

			$this->info($path);

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
			if ($this->input->getOption('seed'))
			{
				$this->call('module:seed '.$slug, ['--force' => true]);
			}
		} else {
			return $this->error("Module [$moduleName] does not exist.");
		}		
	}

	/**
	 * Get migration directory path.
	 *
	 * @param string $slug
	 * @return string
	 */
	protected function getMigrationPath($slug)
	{
		$path = $this->module->getModulePath($slug);

		return $path.'Database/Migrations/';
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

		if ($option = $this->option('database')) {
			$params['--database'] = $option;
		}

		if ($option = $this->option('force')) {
			$params['--force'] = $option;
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
			['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
			['pretend', null, InputOption::VALUE_OPTIONAL, 'Dump the SQL queries that would be run.'],
			['seed', null, InputOption::VALUE_OPTIONAL, 'Indicates if the seed task should be re-run.']
		];
	}
}
