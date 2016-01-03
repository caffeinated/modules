<?php
namespace Caffeinated\Modules\Repositories;

use Caffeinated\Modules\Repositories\Repository;

class LocalRepository extends Repository
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
        $moduleCache = $this->getCache();

        $modules = $this->all()->map(function($item, $key) use ($moduleCache) {
            $item['enabled'] = $moduleCache->get($item['slug']);

            return $item;
        });

		return $modules->where('enabled', true);
	}

	/**
	 * Get all disabled modules.
	 *
	 * @return Collection
	 */
	public function disabled()
	{
        $moduleCache = $this->getCache();

        $modules = $this->all()->map(function($item, $key) use ($moduleCache) {
            $item['enabled'] = $moduleCache->get($item['slug']);

            return $item;
        });

		return $modules->where('enabled', false);
	}

	/**
	 * Check if specified module is enabled.
	 *
	 * @param  string $slug
	 * @return bool
	 */
	public function isEnabled($slug)
	{
        $moduleCache = $this->getCache();

        return $moduleCache->get($slug) === true;
	}

	/**
	 * Check if specified module is disabled.
	 *
	 * @param  string $slug
	 * @return bool
	 */
	public function isDisabled($slug)
	{
        $moduleCache = $this->getCache();

        return $moduleCache->get($slug) === false;
	}

	/**
	 * Enables the specified module.
	 *
	 * @param  string $slug
	 * @return bool
	 */
	public function enable($slug)
	{
        return $this->setCache($slug, true);
	}

	/**
	 * Disables the specified module.
	 *
	 * @param  string $slug
	 * @return bool
	 */
	public function disable($slug)
	{
        return $this->setCache($slug, false);
	}

    /**
     * Refresh the cache with any newly found modules.
     *
     * @return bool
     */
    public function cache()
    {
        $cacheFile = storage_path('app/modules.json');
        $cache     = $this->getCache();
        $modules   = $this->all();

        $collection = collect([]);

        foreach ($modules as $module) {
            $collection->put($module['slug'], true);
        }

        $keys    = $collection->keys()->toArray();
        $merged  = $collection->merge($cache)->only($keys);
        $content = json_encode($merged->all(), JSON_PRETTY_PRINT);

        return $this->files->put($cacheFile, $content);
    }

    /**
     * Get the contents of the cache file.
     *
     * The cache file lists all module slugs and their
     * enabled or disabled status. This can be used to
     * filter out modules depending on their status.
     *
     * @return Collection
     */
    public function getCache()
    {
        $cacheFile = storage_path('app/modules.json');

        if (! $this->files->exists($cacheFile)) {
            $modules = $this->all();
            $content = [];

            foreach ($modules as $module) {
                $content[$module['slug']] = true;
            }

            $content = json_encode($content, JSON_PRETTY_PRINT);

            $this->files->put($cacheFile, $content);

            return collect(json_decode($content, true));
        }

        return collect(json_decode($this->files->get($cacheFile), true));
    }

    /**
     * Set the given cache key value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int
     */
    public function setCache($key, $value)
    {
        $cacheFile = storage_path('app/modules.json');
        $content   = $this->getCache();

        $content->put($key, $value);

        $content = json_encode($content, JSON_PRETTY_PRINT);

        return $this->files->put($cacheFile, $content);
    }
}
