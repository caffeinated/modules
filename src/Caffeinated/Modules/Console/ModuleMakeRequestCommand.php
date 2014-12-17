<?php
namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Handlers\ModuleMakeRequestHandler;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMakeRequestCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:make-request';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new module form request class';

	/**
	 * @var ModuleMakeRequestHandler
	 */
	protected $handler;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(ModuleMakeRequestHandler $handler)
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
		return $this->handler->fire($this, $this->argument('module'), $this->argument('name'));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['module', InputArgument::REQUIRED, 'The slug of the module'],
			['name', InputArgument::REQUIRED, 'The name of the class']
		];
	}
}
