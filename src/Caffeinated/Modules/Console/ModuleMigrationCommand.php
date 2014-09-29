<?php
namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMakeMigrationCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:make-migration';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new module migration file';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Modules $modules, File $files)
	{
		parent::__construct();

		$this->module = $modules;
		$this->file   = $files;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->moduleName    = Str::studly($this->argument('module'));
		$this->table         = str_plural(strtolower($this->argument('table')));
		$this->migrationName = 'create_'.snake_case($this->table).'_table';
		$this->className     = studly_case($this->migrationName);

		if ($this->module->has($this->moduleName)) {
			$this->makeFile();

			$this->info("Created Module Migration: [$this->moduleName] ".$this->getFilename());

			return $this->call('dump-autoload');
		}

		return $this->info("Module [$this->moduleName] does not exist.");
	}

	/**
	 * Create new migration file.
	 *
	 * @return string
	 */
	protected function makeFile()
	{
		return $this->file->put($this->getDestinationFile(), $this->getStubContent());
	}

	/**
	 * Get file destination.
	 *
	 * @return string
	 */
	protected function getDestinationFile()
	{
		return $this->getPath().$this->formatContent($this->getFilename());
	}

	/**
	 * Get module migration path.
	 *
	 * @return string
	 */
	protected function getPath()
	{
		$path = $this->module->getModulePath($this->moduleName);

		return $path.'Database/Migrations/';
	}

	/**
	 * Get migration filename.
	 *
	 * @return string
	 */
	protected function getFilename()
	{
		return date("Y_m_d_His").'_'.$this->migrationName.'.php';
	}

	/**
	 * Get stub content.
	 *
	 * @return string
	 */
	protected function getStubContent()
	{
		return $this->formatContent($this->file->get(__DIR__.'/stubs/migration.stub'));
	}

	/**
	 * Replace placeholder text with correct values.
	 *
	 * @return string
	 */
	protected function formatContent($content)
	{
		return str_replace(
			['{{migrationName}}', '{{table}}'],
			[$this->className, $this->table],
			$content
		);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['module', InputArgument::REQUIRED, 'Module slug.'],
			['table', InputArgument::REQUIRED, 'Table name.']
		];
	}
}