<?php

namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;

class ModuleListCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:list';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List all application modules';

	/**
	 * The modules instance.
	 *
	 * @var \Caffeinated\Modules\Modules
	 */
	protected $module;

	/**
	 * An array of all the registered modules.
	 *
	 * @var Collection
	 */
	protected $modules;

	/**
	 * The table headers for the command.
	 *
	 * @var array
	 */
	protected $headers = ['Name', 'Slug', 'Description', 'Status'];

	/**
	 * Create a new command instance.
	 *
	 * @param Modules $module
	 * @return void
	 */
	public function __construct(Modules $module)
	{
		parent::__construct();

		$this->module  = $module;
		$this->modules = $module->all();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if (count($this->modules) == 0)
		{
			return $this->error("Your application doesn't have any modules.");
		}

		$this->displayModules($this->getModules());
	}

	protected function getModules()
	{
		$results = array();

		foreach ($this->modules as $module)
		{
			$results[] = $this->getModuleInformation($module);
		}

		return array_filter($results);
	}

	protected function getModuleInformation($module)
	{
		return [
			'name'        => $module['name'],
			'slug'        => $module['slug'],
			'description' => $module['description'],
			'status'      => ($module['enabled']) ? 'Enabled' : 'Disabled'
		];
	}

	/**
	 * Display the module information on the console.
	 *
	 * @param array $modules
	 * @return void
	 */
	protected function displayModules(array $modules)
	{
		$this->table($this->headers, $modules);
	}
}