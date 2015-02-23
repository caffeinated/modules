<?php
namespace Caffeinated\Modules;

use App;
use Countable;
use Caffeinated\Modules\Exceptions\FileMissingException;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Modules implements Countable
{
	/**
	 * @var \Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * @var string $path Path to the defined modules directory
	 */
	protected $path;

	/**
	 * Constructor method.
	 *
	 * @param \Illuminate\Config\Repository                $config
	 * @param \Illuminate\Filesystem\Filesystem            $files
	 */
	public function __construct(Repository $config, Filesystem $files)
	{
		$this->config = $config;
		$this->files  = $files;
	}

	/**
	 * Register the module service provider file from all modules.
	 *
	 * @return mixed
	 */
	public function register()
	{
		foreach ($this->enabled() as $module) {
			$this->registerServiceProvider($module);
		}
	}

	/**
	 * Register the module service provider.
	 *
	 * @param  string $module
	 * @return string
	 * @throws \Caffeinated\Modules\Exception\FileMissingException
	 */
	protected function registerServiceProvider($module)
	{
		$module    = Str::studly($module['slug']);
		$file      = $this->getPath()."/{$module}/Providers/{$module}ServiceProvider.php";
		$namespace = $this->getNamespace().$module."\\Providers\\{$module}ServiceProvider";

		if (! $this->files->exists($file)) {
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
		$modules    = array();
		$allModules = $this->getAllBasenames();

		foreach ($allModules as $module) {
			$modules[] = $this->getJsonContents($module);
		}

		return new Collection($modules);
	}

	/**
	 * Get all module basenames
	 *
	 * @return array
	 */
	protected function getAllBasenames()
	{
		$modules = [];
		$path    = $this->getPath();

		if ( ! is_dir($path))
			return $modules;

		$folders = $this->files->directories($path);

		foreach ($folders as $module) {
			$modules[] = basename($module);
		}

		return $modules;
	}

	/**
	 * Get all module slugs.
	 *
	 * @return array
	 */
	protected function getAllSlugs()
	{
		$modules = $this->all();
		$slugs   = array();

		foreach ($modules as $module)
		{
			$slugs[] = $module['slug'];
		}

		return $slugs;
	}

	/**
	 * Check if given module path exists.
	 *
	 * @param  string  $folder
	 * @return bool
	 */
	protected function pathExists($folder)
	{
		$folder = Str::studly($folder);

		return in_array($folder, $this->getAllBasenames());
	}

	/**
	 * Check if the given module exists.
	 *
	 * @param  string  $slug
	 * @return bool
	 */
	public function exists($slug)
	{
		$slug = strtolower($slug);

		return in_array($slug, $this->getAllSlugs());
	}

	/**
	 * Returns count of all modules.
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
		return $this->path ?: $this->config->get('modules.path');
	}

	/**
	 * Set modules path in "RunTime" mode.
	 *
	 * @param  string $path
	 * @return object $this
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Get modules namespace.
	 *
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->config->get('modules.namespace');
	}

	/**
	 * Get path for the specified module.
	 *
	 * @param  string $slug
	 * @return string
	 */
	public function getModulePath($slug, $allowNotExists = false)
	{
		$module = Str::studly($slug);

		if ( ! $this->pathExists($module) and $allowNotExists === false)
			return null;

		return $this->getPath()."/{$module}/";
	}

	/**
	 * Get a module's properties.
	 *
	 * @param  string $slug
	 * @return mixed
	 */
	public function getProperties($slug)
	{
		return $this->getJsonContents($slug);
	}

	/**
	 * Get a module property value.
	 *
	 * @param  string $property
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function getProperty($property, $default = null)
	{
		list($module, $key) = explode('::', $property);

		return array_get($this->getJsonContents($module), $key, $default);
	}

	/**
	 * Set a module property value.
	 *
	 * @param  string $property
	 * @param  mixed  $value
	 * @return bool
	 */
	public function setProperty($property, $value)
	{
		list($module, $key) = explode('::', $property);

		$content = $this->getJsonContents($module);

		if (count($content)) {
			if (isset($content[$key])) {
				unset($content[$key]);
			}

			$content[$key] = $value;

			$this->setJsonContents($module, $content);

			return true;
		}

		return false;
	}

	/**
	 * Get all modules by enabled status.
	 *
	 * @param  bool $enabled
	 * @return array
	 */
	public function getByEnabled($enabled = true)
	{
		$disabledModules = array();
		$enabledModules  = array();
		$modules         = $this->all();
		$order           = array();

		foreach ($modules as $module) {
			if (! isset($module['order'])) {
				// It's over 9000!
				$module['order'] = 9001;
			}

			if ($this->isEnabled($module['slug'])) {
				$enabledModules[] = $module;
				$order[]          = $module['order'];
			} else {
				$disabledModules[] = $module;
			}
		}

		if ($enabled === true) {
			if (count($enabledModules) > 0) {
				array_multisort($order, SORT_ASC, $enabledModules);
			}

			return $enabledModules;
		}

		return $disabledModules;
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
	 * @param  string $slug
	 * @return bool
	 */
	public function isEnabled($slug)
	{
		return $this->getProperty("{$slug}::enabled") === true;
	}

	/**
	 * Check if specified module is disabled.
	 *
	 * @param  string $slug
	 * @return bool
	 */
	public function isDisabled($slug)
	{
		return $this->getProperty("{$slug}::enabled") === false;
	}

	/**
	 * Enables the specified module.
	 *
	 * @param  string $slug
	 * @return bool
	 */
	public function enable($slug)
	{
		return $this->setProperty("{$slug}::enabled", true);
	}

	/**
	 * Disables the specified module.
	 *
	 * @param  string $slug
	 * @return bool
	 */
	public function disable($slug)
	{
		return $this->setProperty("{$slug}::enabled", false);
	}

	/**
	 * Get module JSON content as an array.
	 *
	 * @param  string $module
	 * @return array|mixed
	 */
	protected function getJsonContents($module)
	{
		$module = Str::studly($module);

		$default = [];

		if ( ! $this->pathExists($module))
			return $default;

		$path = $this->getJsonPath($module);

		if ($this->files->exists($path)) {
			$contents = $this->files->get($path);

			return json_decode($contents, true);
		} else {
			$message = "Module [{$module}] must have a valid module.json file.";

			throw new FileMissingException($message);
		}
	}

	/**
	 * Set module JSON content property value.
	 *
	 * @param  string $module
	 * @param  array  $content
	 * @return int
	 */
	public function setJsonContents($module, array $content)
	{
		$module = strtolower($module);
		$content = json_encode($content, JSON_PRETTY_PRINT);

		return $this->files->put($this->getJsonPath($module), $content);
	}

	/**
	 * Get path of module JSON file.
	 *
	 * @param  string $module
	 * @return string
	 */
	protected function getJsonPath($module)
	{
		return $this->getModulePath($module).'/module.json';
	}
}
