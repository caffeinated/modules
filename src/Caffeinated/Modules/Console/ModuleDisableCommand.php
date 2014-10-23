<?php

namespace Caffeinated\Modules\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ModuleDisableCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:disable';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Disable a module';

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
		$module = $this->argument('module');

		if ($this->laravel['modules']->isEnabled($this->argument('module'))) {
			$this->laravel['modules']->disable($module);

			$this->info("Module [{$module}] was disabled successfully.");
		} else {
			$this->comment("Module [{$module}] is already disabled.");
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['module', InputArgument::REQUIRED, 'Module slug.']
		];
	}
}
