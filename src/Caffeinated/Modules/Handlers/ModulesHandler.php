<?php
namespace Caffeinated\Modules\Handlers;

use Countable;
use Caffeinated\Modules\Exceptions\FileMissingException;
use Illuminate\Support\Str;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection;

class ModulesHandler implements Countable
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
	 * Constructor method.
	 *
	 * @param Filesystem $files
	 * @param Repository $config
	 */
	public function __construct(Filesystem $files, Repository $config)
	{
		$this->config = $config;
		$this->files  = $files;
	}

	/**
	 * Get all modules.
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
			$modules[] = basename($module);
		}
		
		return new Collection($modules);
	}

	/**
	 * Check if given module exists.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function exists($slug)
	{
		return in_array($slug, $this->all()->toArray());
	}

	/**
	 * Return count of all modules.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->all()->toArray());
	}

	/**
	 * Gets module path.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path ?: $this->config->get('caffeinated::modules.path');
	}

	/**
	 * Sets module path.
	 *
	 * @param string $path
	 * @return self
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Gets the path of specified module.
	 *
	 * @param string $module
	 * @param bool $allowNotExists
	 * @return null|string
	 */
	public function getModulePath($module, $allowNotExists = false)
	{
		$module = Str::studly($module);

		if ( ! $this->exists($module) && $allowNotExists === false)
			return null;

		return $this->getPath()."/{$module}/";
	}

	/**
	 * Get a module property value.
	 *
	 * @param string $property
	 * @param null|String $default
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
	 * @param string $property
	 * @param mixed $value
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
	 * Get module JSON content as an array.
	 *
	 * @param string $module
	 * @return array|mixed
	 */
	public function getJsonContents($module)
	{
		$module = Str::studly($module);

		$default = [];

		if ( ! $this->exists($module))
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
	 * @param $module
	 * @param array $content
	 * @return int
	 */
	public function setJsonContents($module, array $content)
	{
		$content = json_encode($content, JSON_PRETTY_PRINT);

		return $this->files->put($this->getJsonPath($module), $content);
	}

	/**
	 * Get path of module JSON file.
	 *
	 * @param string $module
	 * @return string
	 */
	public function getJsonPath($module)
	{
		return $this->getModulePath($module).'/module.json';
	}

	/**
	 * Enables the specified module.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function enable($slug)
	{
		return $this->setProperty("{$slug}::enabled", true);
	}

	/**
	 * Disables the specified module.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function disable($slug)
	{
		return $this->setProperty("{$slug}::enabled", false);
	}
}
