<?php
namespace Caffeinated\Modules;

use Countable;
use Caffeinated\Modules\Exceptions\FileMissingException;
use Illuminate\View\Factory;
use Illuminate\Config\Repository;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\Translator;
use Illuminate\Database\Eloquent\Collection;

class Modules implements Countable
{
	/**
	 * @var Finder
	 */
	protected $finder;

	/**
	 * @var Repository
	 */
	protected $config;

	/**
	 * @var Translator
	 */
	protected $lang;

	/**
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * @var Factory
	 */
	protected $views;

	/**
	 * @var UrlGenerator
	 */
	protected $url;

	/**
	 * Constructor method
	 *
	 * @param Finder $finder
	 * @param Repository $config
	 * @param Factory $view
	 * @param Translator $lang
	 */
	public function __construct(
		Finder $finder,
		Repository $config,
		Factory $views,
		Translator $lang,
		Filesystem $files,
		UrlGenerator $url
	) {
		$this->finder = $finder;
		$this->config = $config;
		$this->lang   = $lang;
		$this->views  = $views;
		$this->files  = $files;
		$this->url    = $url;
	}

	/**
	 * Register the global.php file from all modules
	 *
	 * @return Mixed
	 */
	public function register()
	{
		foreach ($this->enabled() as $module) {
			$this->includeGlobalFile($module);
		}
	}

	/**
	 * Get global.php file for the specified module
	 *
	 * @param String $slug
	 * @return String
	 * @throws \Caffeinated\Modules\FileMissingException
	 */
	protected function includeGlobalFile($slug)
	{
		$file = $this->getPath()."/{$slug}/start/global.php";

		if ( ! $this->files->exists($file)) {
			$message = "Module [{$slug}] must have a start/global.php file for bootstrapping purposes.";

			throw new FileMissingException($message);
		}

		require $file;
	}

	/**
	 * Get all modules
	 *
	 * @return Collection
	 */
	public function all()
	{
		foreach ($this->finder->all() as $module) {
			$modules[] = $this->finder->getJsonContents($module);
		}

		if (isset($modules)) {
			$collection = new Collection($modules);
		}		

		return [];
	}

	/**
	 * Check if given module exists
	 *
	 * @param String $slug
	 * @return Bool
	 */
	public function has($slug)
	{
		return $this->finder->has($slug);
	}

	/**
	 * Count all modules
	 * 
	 * @return Int
	 */
	public function count()
	{
		return count($this->all());
	}

	/**
	 * Get modules path
	 *
	 * @return String
	 */
	public function getPath()
	{
		return $this->config->get('modules::paths.modules');
	}

	/**
	 * Set modules path in "RunTime" mode
	 *
	 * @param String $path
	 * @return $this
	 */
	public function setPath($path)
	{
		$this->finder->setPath($path);

		return $this;
	}

	/**
	 * Get path for the specified module
	 *
	 * @param String $slug
	 * @return String
	 */
	public function getModulePath($slug)
	{
		return $this->finder->getModulePath($slug, true);
	}

	/**
	 * Get a module's properties
	 *
	 * @param String $slug
	 * @return Mixed
	 */
	public function getProperties($slug)
	{
		return $this->finder->getJsonContents($slug);
	}

	/**
	 * Get a module property value
	 *
	 * @param String $key
	 * @param Null $default
	 * @return Mixed
	 */
	public function getProperty($key, $default = null)
	{
		return $this->finder->getProperty($key, $default);
	}

	/**
	 * Set a module property value
	 *
	 * @param String $key
	 * @param Mixed $value
	 * @return Mixed
	 */
	public function setProperty($key, $value)
	{
		return $this->finder->setProperty($key, $value);
	}

	/**
	 * Get all modules by enabled status
	 *
	 * @param Bool $enabled
	 * @return Array
	 */
	public function getByEnabled($enabled = true)
	{
		$data = [];

		foreach ($this->all() as $module) {
			if ($enabled === true) {
				if ($this->isEnabled($module))
					$data[] = $module;
			} else {
				if ($this->isDisabled($module))
					$data[] = $module;
			}
		}

		return $data;
	}

	/**
	 * Simple alias for getByEnabled(true)
	 *
	 * @return Array
	 */
	public function enabled()
	{
		return $this->getByEnabled(true);
	}

	/**
	 * Simple alias for getByEnabled(false)
	 *
	 * @return Array
	 */
	public function disabled()
	{
		return $this->getByEnabled(false);
	}

	/**
	 * Check if specified module is enabled
	 *
	 * @param String $slug
	 * @return Bool
	 */
	public function isEnabled($slug)
	{
		return $this->getProperty("{$slug}::enabled") == true;
	}

	/**
	 * Check if specified module is disabled
	 *
	 * @param String $slug
	 * @return Bool
	 */
	public function isDisabled($slug)
	{
		return $this->getProperty("{$slug}::enabled") == false;
	}

	/**
	 * Enables the specified module
	 *
	 * @param String $slug
	 * @return Bool
	 */
	public function enable($slug)
	{
		return $this->finder->enable($slug);
	}

	/**
	 * Disables the specified module
	 *
	 * @param String $slug
	 * @return Bool
	 */
	public function disable($slug)
	{
		return $this->finder->disable($slug);
	}
}