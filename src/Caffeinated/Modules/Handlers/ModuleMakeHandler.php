<?php
namespace Caffeinated\Modules\Handlers;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModuleMakeHandler
{
	/**
	 * Module folders to be created.
	 *
	 * @var array
	 */
	protected $folders = [
		'Console/',
		'Database/',
		'Database/Migrations/',
		'Database/Seeds',
		'Http/',
		'Http/Controllers/',
		'Http/Middleware/',
		'Http/Requests/',
		'Providers/',
		'Resources/',
		'Resources/Lang/',
		'Resources/Views/',
	];

	/**
	 * Module files to be created.
	 *
	 * @var array
	 */
	protected $files = [
		'Database/Seeds/{{name}}DatabaseSeeder.php',
		'Http/routes.php',
		'Providers/{{name}}ServiceProvider.php',
		'Providers/RouteServiceProvider.php',
		'module.json'
	];

	/**
	 * Module stubs used to populate defined files.
	 *
	 * @var array
	 */
	protected $stubs = [
		'seeder.stub',
		'routes.stub',
		'moduleserviceprovider.stub',
		'routeserviceprovider.stub',
		'module.stub'
	];

	/**
	 * @var Modules
	 */
	protected $module;

	/**
	 * @var Filesystem
	 */
	protected $finder;

	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * Constructor method.
	 *
	 * @param Modules $module
	 * @param Filesystem $finder
	 */
	public function __construct(Modules $module, Filesystem $finder)
	{
		$this->module = $module;
		$this->finder  = $finder;
	}

	/**
	 * Fire off the handler.
	 *
	 * @param  \Caffeinated\Modules\Console\ModuleMakeCommand $console
	 * @param  string $slug
	 * @return bool
	 */
	public function fire(Command $console, $slug)
	{
		$this->console = $console;
		$this->slug    = $slug;
		$this->name    = Str::studly($slug);

		if ($this->module->exists($this->slug)) {
			$console->comment('Module [{$this->name}] already exists.');

			return false;
		}

		$this->generate($console);
	}

	/**
	 * Generate module folders and files.
	 *
	 * @param  \Caffeinated\Modules\Console\ModuleMakeCommand $console
	 * @return boolean
	 */
	public function generate(Command $console)
	{
		$this->generateFolders();

		$this->generateFiles();

		$console->info("Module [{$this->name}] has been created successfully.");

		return true;
	}

	/**
	 * Generate defined module folders.
	 *
	 * @return void
	 */
	protected function generateFolders()
	{
		if ( ! $this->finder->isDirectory($this->module->getPath()))
			$this->finder->makeDirectory($this->module->getPath());

		$this->finder->makeDirectory($this->getModulePath($this->slug));

		foreach ($this->folders as $folder) {
			$this->finder->makeDirectory($this->getModulePath($this->slug).$folder);
		}
	}

	/**
	 * Generate defined module files.
	 *
	 * @return void
	 */
	protected function generateFiles()
	{
		foreach ($this->files as $key => $file) {
			$file = $this->formatContent($file);

			$this->makeFile($key, $file);
		}
	}

	/**
	 * Create module file.
	 *
	 * @param  integer $key
	 * @param  string $file
	 * @return integer
	 */
	protected function makeFile($key, $file)
	{
		return $this->finder->put($this->getDestinationFile($file), $this->getStubContent($key));
	}

	/**
	 * Get the path to the module.
	 *
	 * @param  string $slug
	 * @return string
	 */
	protected function getModulePath($slug = null)
	{
		if ($slug)
			return $this->module->getModulePath($slug);

		return $this->module->getPath();
	}

	/**
	 * Get destination file.
	 *
	 * @param  string $file
	 * @return string
	 */
	protected function getDestinationFile($file)
	{
		return $this->getModulePath($this->slug).$this->formatContent($file);
	}

	/**
	 * Get stub content by key.
	 *
	 * @param  integer $key
	 * @return string
	 */
	protected function getStubContent($key)
	{
		return $this->formatContent($this->finder->get(__DIR__.'/../Console/stubs/'.$this->stubs[$key]));
	}

	/**
	 * Replace placeholder text with correct values.
	 *
	 * @return string
	 */
	protected function formatContent($content)
	{
		return str_replace(
			['{{slug}}', '{{name}}', '{{namespace}}'],
			[$this->slug, $this->name, $this->module->getNamespace()],
			$content
		);
	}
}
