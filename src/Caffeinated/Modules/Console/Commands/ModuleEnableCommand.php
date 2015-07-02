<?php
namespace Caffeinated\Modules\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ModuleEnableCommand extends Command
{
	/**
	 * @var string $name The console command name.
	 */
	protected $name = 'module:enable';

	/**
	 * @var string $description The console command description.
	 */
	protected $description = 'Enable a module';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$module = $this->argument('module');

		if ($this->laravel['modules']->isDisabled($this->argument('module'))) {
			$this->laravel['modules']->enable($module);

			$this->info("Module [{$module}] was enabled successfully.");
		} else {
			$this->comment("Module [{$module}] is already enabled.");
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
