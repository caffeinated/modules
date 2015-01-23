<?php
namespace Caffeinated\Modules\Console;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;

class ModuleListCommand extends Command
{
	/**
	 * @var string $name The console command name.
	 */
	protected $name = 'module:list';

	/**
	 * @var string $description The console command description.
	 */
	protected $description = 'List all application modules';

	/**
	 * @var \Caffeinated\Modules\Modules
	 */
	protected $module;

	/**
	 * @var array $header The table headers for the command.
	 */
	protected $headers = ['Name', 'Slug', 'Description', 'Status'];

	/**
	 * Create a new command instance.
	 *
	 * @param \Caffeinated\Modules\Modules $module
	 */
	public function __construct(Modules $module)
	{
		parent::__construct();

		$this->module = $module;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$modules = $this->module->all();

		if (count($modules) == 0)
		{
			return $this->error("Your application doesn't have any modules.");
		}

		$this->displayModules($this->getModules());
	}

	/**
	 * Get all modules.
	 *
	 * @return array
	 */
	protected function getModules()
	{
		$modules = $this->module->all();
		$results = array();

		foreach ($modules as $module)
		{
			$results[] = $this->getModuleInformation($module);
		}

		return array_filter($results);
	}

	/**
	 * Returns module manifest information.
	 *
	 * @param  string $module
	 * @return array
	 */
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
	 * @param  array $modules
	 * @return void
	 */
	protected function displayModules(array $modules)
	{
		$this->table($this->headers, $modules);
	}
}
