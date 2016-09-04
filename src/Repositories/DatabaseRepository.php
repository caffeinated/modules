<?php

namespace Caffeinated\Modules\Repositories;

use Caffeinated\Modules\Models\Module;

class DatabaseRepository extends Repository
{
    /**
     * Get all modules.
     *
     * @return Collection
     */
    public function all()
    {
        return Module::orderBy('order')->all();
    }

    /**
     * Get all module slugs.
     *
     * @return Collection
     */
    public function slugs()
    {
        $slugs = collect();

        $this->all()->each(function ($item, $key) use ($slugs) {
            $slugs->push($item['slug']);
        });

        return $slugs;
    }

    /**
     * Get modules based on where clause.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Collection
     */
    public function where($key, $value)
    {
        return $this->all()->where($key, $value)->first();
    }

    /**
     * Sort modules by given key in ascending order.
     *
     * @param string $key
     *
     * @return Collection
     */
    public function sortBy($key)
    {
        $collection = $this->all();

        return $collection->orderBy($key);
    }

    /**
     * Sort modules by given key in ascending order.
     *
     * @param string $key
     *
     * @return Collection
     */
    public function sortByDesc($key)
    {
        $collection = $this->all();

        return $collection->orderBy($key, 'desc');
    }

    /**
     * Determines if the given module exists.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function exists($slug)
    {
        return $this->slugs()->contains(str_slug($slug));
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
     * Get a module property value.
     *
     * @param string $property
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($property, $default = null)
    {
        list($slug, $key) = explode('::', $property);

        $module = $this->where('slug', $slug);

        switch ($key) {
            case 'name':
                $value = $module->name;
                break;
            case 'slug':
                $value = $module->slug;
                break;
            case 'enabled':
                $value = $module->enabled;
                break;
            default:
                $value = $module->manifest[$property];
                break;
        }

        if (! $value) return $default;

        return $value;
    }

    /**
     * Set the given module property value.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return bool
     */
    public function set($property, $value)
    {
        list($slug, $key) = explode('::', $property);

        $module = Module::where('slug', $slug)->first();

        switch ($key) {
            case 'name':
                $module->name = $value;
                break;
            case 'slug':
                $module->slug = $value;
                break;
            case 'enabled':
                $module->enabled = $value;;
                break;
            default:
                if (isset($module->manifest[$key])) {
                    unset($module->manifest[$key]);
                }

                $module->manifest[$key] = $value;
                break;
        }

        $module->save();
    }

    /**
     * Get all enabled modules.
     *
     * @return Collection
     */
    public function enabled()
    {
        return $this->all()->where('enabled', true);
    }

    /**
     * Get all disabled modules.
     *
     * @return Collection
     */
    public function disabled()
    {
        return $this->all()->where('enabled', false);
    }

    /**
     * Check if specified module is enabled.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isEnabled($slug)
    {
        $module = $this->where('slug', $slug);

        return $module->enabled === true;
    }

    /**
     * Check if specified module is disabled.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isDisabled($slug)
    {
        $module = $this->where('slug', $slug);

        return $module->enabled === false;
    }

    /**
     * Enables the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function enable($slug)
    {
        return $this->set($slug.'::enabled', true);
    }

    /**
     * Disables the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function disable($slug)
    {
        return $this->set($slug.'::enabled', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Optimization Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Update cached repository of module information.
     *
     * @return bool
     */
    public function optimize()
    {
        $basenames = $this->getAllBasenames();
        $modules   = collect();

        $basenames->each(function ($module, $key) use ($modules, $cache) {
            $basename = collect(['basename' => $module]);
            $temp     = $basename->merge(collect($cache->get($module)));
            $manifest = $temp->merge(collect($this->getManifest($module)));

            $modules->put($module, $manifest);
        });

        $modules->each(function ($module) {
            if (! $module->has('enabled')) {
                $module->put('enabled', config('modules.enabled', true));
            }

            if (! $module->has('order')) {
                $module->put('order', 9001);
            }

            return $module;
        });

        return $this->saveOrUpdateModules($modules->all());
    }

    private function saveOrUpdateModules($modules)
    {
        foreach ($modules as $module) {
            $instance = Module::whereSlug($module['slug'])->firstOrCreate();

            $data = [
                'name'     => $module['name'],
                'slug'     => $module['slug'],
                'basename' => $module['basename'],
                'enabled'  => $module['enabled'],
            ];

            unset($module['name'], $module['slug'], $module['basename'], $module['enabled']);

            $data['manifest'] = $module;

            $instance->update($data);
        }

        // Remove any lingering modules that don't exist
        Module::where('slug', '!=', $modules['slug'])->delete();

        return true;
    }
}
