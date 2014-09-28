<?php
namespace Caffeinated\Modules;

use App;
use Countable;
use Caffeinated\Modules\Exceptions\FileMissingException;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;
use Illuminate\View\Factory;

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
	 * Constructor method.
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
	 * Register the global.php file from all modules.
	 *
	 * @return mixed
	 */
	public function register()
	{
		foreach ($this->enabled() as $module) {
			$this->includeGlobalFile($module);
		}
	}

	/**
	 * Get global.php file for the specified module.
	 *
	 * @param array $module
	 * @return string
	 * @throws \Caffeinated\Modules\Exception\FileMissingException
	 */
	protected function includeGlobalFile($module)
	{
		$module = Str::studly($module['slug']);

		$file = $this->getPath()."/{$module}/Providers/{$module}ServiceProvider.php";

		$namespace = $this->getNamespace().$module."\\Providers\\{$module}ServiceProvider";

		if ( ! $this->files->exists($file)) {
			$message = "Module [{$module}] must have a \"{$module}/Providers/{$module}ServiceProvider.php\" file for bootstrapping purposes.";

			throw new FileMissingException($message);
		}

		App::register($namespace);
	}

	/**
	 * Get all modules.
	 *
	 * @return Collection
	 */
	public function all()
	{
		foreach ($this->finder->all() as $module) {
			$modules[] = $this->finder->getJsonContents($module);
		}

		if (isset($modules))
			return new Collection($modules);
	}

	/**
	 * Check if given module exists.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function has($slug)
	{
		return $this->finder->has($slug);
	}

	/**
	 * Count all modules.
	 * 
	 * @return int
	 */
	public function count()
	{
		return count($this->all());
	}

	/**
	 * Get modules path.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->config->get('modules::paths.modules');
	}

	/**
	 * Set modules path in "RunTime" mode.
	 *
	 * @param string $path
	 * @return $this
	 */
	public function setPath($path)
	{
		$this->finder->setPath($path);

		return $this;
	}

	/**
	 * Get modules namespace.
	 *
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->config->get('modules::namespaces.modules');
	}

	/**
	 * Get path for the specified module.
	 *
	 * @param string $slug
	 * @return string
	 */
	public function getModulePath($slug)
	{
		return $this->finder->getModulePath($slug, true);
	}

	/**
	 * Get a module's properties.
	 *
	 * @param string $slug
	 * @return mixed
	 */
	public function getProperties($slug)
	{
		return $this->finder->getJsonContents($slug);
	}

	/**
	 * Get a module property value.
	 *
	 * @param string $key
	 * @param null $default
	 * @return mixed
	 */
	public function getProperty($key, $default = null)
	{
		return $this->finder->getProperty($key, $default);
	}

	/**
	 * Set a module property value.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	public function setProperty($key, $value)
	{
		return $this->finder->setProperty($key, $value);
	}

	/**
	 * Get all modules by enabled status.
	 *
	 * @param bool $enabled
	 * @return array
	 */
	public function getByEnabled($enabled = true)
	{
		$data    = [];
		$modules = $this->all();

		if (count($modules)) {
			foreach ($modules as $module) {
				if ($enabled === true) {
					if ($this->isEnabled($module['slug']))
						$data[] = $module;
				} else {
					if ($this->isDisabled($module['slug']))
						$data[] = $module;
				}
			}
		}

		return $data;
	}

	/**
	 * Simple alias for getByEnabled(true).
	 *
	 * @return array
	 */
	public function enabled()
	{
		return $this->getByEnabled(true);
	}

	/**
	 * Simple alias for getByEnabled(false).
	 *
	 * @return array
	 */
	public function disabled()
	{
		return $this->getByEnabled(false);
	}

	/**
	 * Check if specified module is enabled.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function isEnabled($slug)
	{
		return $this->getProperty("{$slug}::enabled") == true;
	}

	/**
	 * Check if specified module is disabled.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function isDisabled($slug)
	{
		return $this->getProperty("{$slug}::enabled") == false;
	}

	/**
	 * Enables the specified module.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function enable($slug)
	{
		return $this->finder->enable($slug);
	}

	/**
	 * Disables the specified module.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function disable($slug)
	{
		return $this->finder->disable($slug);
	}
}