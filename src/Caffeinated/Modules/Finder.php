<?php
namespace Caffeinated\Modules;

use Countable;
use Illuminate\Support\Str;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection;

class Finder implements Countable
{
	/**
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * @var Repository
	 */
	protected $config;

	/**
	 * @var String
	 */
	protected $path;

	/**
	 * Constructor method
	 *
	 * @param Filesystem $files
	 * @param Repository $confid
	 */
	public function __construct(Filesystem $files, Repository $config)
	{
		$this->files      = $files;
		$this->config     = $config;
	}

	/**
	 * Get all modules
	 *
	 * @return Collection
	 */
	public function all()
	{
		$modules = [];
		$path    = $this->getPath();

		if ( ! is_dir($path))
			return new Collection($modules);

		$folders = $this->files->directories($path);

		foreach ($folders as $module) {
			if ( ! Str::startWith($module, '.'))
				$modules[] = basename($module);
		}

		return new Collection($modules);
	}

	/**
	 * Check if given module exists
	 *
	 * @param String $slug
	 * @return Bool
	 */
	public function has($slug)
	{
		return in_array($slug, $this->all());
	}

	/**
	 * Return count of all modules
	 *
	 * @return Int
	 */
	public function count()
	{
		return count($this->all());
	}

	/**
	 * Gets module path
	 *
	 * @return String
	 */
	public function getPath()
	{
		return $this->path ?: $this->config->get('modules::paths.modules');
	}

	/**
	 * Gets the path of specified module
	 *
	 * @param String $module
	 * @param Bool $allowNotExists
	 * @return Null|String
	 */
	public function getModulePath($module, $allowNotExists = false)
	{
		$module = Str::studly($module);

		if ( ! $this->has($module) and $allowNotExists === false)
			return null;

		return $this->getPath()."/{$module}/";
	}

	/**
	 * Get a module property value
	 *
	 * @param String $property
	 * @param Null|String $default
	 * @return Mixed
	 */
	public function getProperty($property, $default = null)
	{
		list($module, $key) = explode('::', $key);

		return array_get($this->getJsonContents($module), $key, $default);
	}

	/**
	 * Set a module property value
	 *
	 * @param String $property
	 * @param Mixed $value
	 * @return Bool
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
	 * Get module JSON content as an array
	 *
	 * @param String $module
	 * @return array|mixed
	 */
	public function getJsonContents($module)
	{
		$module  = Str::studly($module);
		
		$default = [];

		if ( ! $this->has($module))
			return $default;

		$path = $this->getJsonPath($module);

		if ($this->files->exists($path)) {
			$contents = $this->files->get($path);

			return json_decode($contents, true);
		}

		return $default;
	}

	/**
	 * Set module JSON content property value
	 *
	 * @param $module
	 * @param Array $content
	 * @return Int
	 */
	public function setJsonContents($module, array $content)
	{
		$content = json_encode($content, JSON_PRETTY_PRINT);

		return $this->files->put($this->getJsonPath($module), $content);
	}

	/**
	 * Get path of module JSON file
	 *
	 * @param String $module
	 * @return String
	 */
	public function getJsonPath($module)
	{
		return $this->getModulePath($module).'/module.json';
	}

	/**
	 * Enables the specified module
	 *
	 * @param String $slug
	 * @return Bool
	 */
	public function enable($slug)
	{
		return $this->setProperty($slug.'::enabled', true);
	}

	/**
	 * Disables the specified module
	 *
	 * @param String $slug
	 * @return Bool
	 */
	public function disable($slug)
	{
		return $this->setProperty($slug.'::enabled', false);
	}
}