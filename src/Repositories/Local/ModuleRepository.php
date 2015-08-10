<?php
namespace Caffeinated\Modules\Repositories\Local;

use Caffeinated\Modules\Repositories\Repository;

class ModuleRepository extends Repository
{
	/**
	* Get all modules.
	*
	* @return Collection
	*/
	public function all()
	{
		$basenames = $this->getAllBasenames();
		$modules   = collect();

		$basenames->each(function($module, $key) use ($modules) {
			$modules->put($module, $this->getProperties($module));
		});

		return $modules->sortBy('order');
	}

	/**
	* Get all module slugs.
	*
	* @return Collection
	*/
	public function slugs()
	{
		$slugs = collect();

		$this->all()->each(function($item, $key) use ($slugs) {
			$slugs->push($item['slug']);
		});

		return $slugs;
	}

	/**
	 * Get modules based on where clause.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return Collection
	 */
	public function where($key, $value)
	{
		$collection = $this->all();

		return $collection->where($key, $value);
	}

	/**
	 * Sort modules by given key in ascending order.
	 *
	 * @param  string  $key
	 * @return Collection
	 */
	public function sortBy($key)
	{
		$collection = $this->all();

		return $collection->sortBy($key);
	}

	/**
	* Sort modules by given key in ascending order.
	*
	* @param  string  $key
	* @return Collection
	*/
	public function sortByDesc($key)
	{
		$collection = $this->all();

		return $collection->sortByDesc($key);
	}

	/**
	 * Determines if the given module exists.
	 *
	 * @param  string  $slug
	 * @return bool
	 */
	public function exists($slug)
	{
		return $this->slugs()->contains(strtolower($slug));
	}

	/**
	 * Returns count of all modules.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->all()->count();
	}

	/**
	 * Get a module's properties.
	 *
	 * @param  string $slug
	 * @return Collection|null
	 */
	public function getProperties($slug)
	{
		if (! is_null($slug)) {
			$module     = studly_case($slug);
			$path       = $this->getManifestPath($module);
			$contents   = $this->files->get($path);
			$collection = collect(json_decode($contents, true));

			if (! $collection->has('order')) {
				$collection->put('order', 9001);
			}

			return $collection;
		}

		return null;
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

		return $this->getProperties($module)->get($key, $default);
	}

	/**
	* Set the given module property value.
	*
	* @param  string  $property
	* @param  mixed   $value
	* @return bool
	*/
	public function setProperty($property, $value)
	{
		list($module, $key) = explode('::', $property);

		$module  = strtolower($module);
		$content = $this->getProperties($module);

		if (isset($content[$key])) {
			unset($content[$key]);
		}

		$content[$key] = $value;
		$content       = json_encode($content, JSON_PRETTY_PRINT);

		return $this->files->put($this->getManifestPath($module), $content);
	}

	/**
	 * Get all enabled modules.
	 *
	 * @return Collection
	 */
	public function enabled()
	{
		return $this->where('enabled', true);
	}

	/**
	 * Get all disabled modules.
	 *
	 * @return Collection
	 */
	public function disabled()
	{
		return $this->where('enabled', false);
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
}
