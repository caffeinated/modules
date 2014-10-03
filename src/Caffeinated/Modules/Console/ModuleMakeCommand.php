<?php

namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Handlers\ModuleMakeHandler;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:make';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new module';

	/**
	 * @var ModuleMakeHandler
	 */
	protected $handler;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(ModuleMakeHandler $handler)
	{
		parent::__construct();

		$this->handler = $handler;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		return $this->handler->fire($this, $this->argument('name'));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['name', InputArgument::REQUIRED, 'Module slug.']
		];
	}
}