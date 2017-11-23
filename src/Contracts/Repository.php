<?php

namespace Caffeinated\Modules\Contracts;

interface Repository
{
    /*
    |--------------------------------------------------------------------------
    | Optimization Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Get all module manifest properties and store
     * in the respective container.
     *
     * @return bool
     */
    public function optimize();

    /*
    |--------------------------------------------------------------------------
    | Collection Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Get all modules.
     *
     * @return Collection
     */
    public function all();

    /**
     * Get all module slugs.
     *
     * @return Collection
     */
    public function slugs();

    /**
     * Get modules based on where clause.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Collection
     */
    public function where($key, $value);

    /**
     * Sort modules by given key in ascending order.
     *
     * @param string $key
     *
     * @return Collection
     */
    public function sortBy($key);

    /**
     * Sort modules by given key in ascending order.
     *
     * @param string $key
     *
     * @return Collection
     */
    public function sortByDesc($key);

    /**
     * Determines if the given module exists.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function exists($slug);

    /**
     * Returns a count of all modules.
     *
     * @return int
     */
    public function count();

    /**
     * Get all module basenames.
     *
     * @return array
     */
    public function getAllBasenames();

    /*
    |--------------------------------------------------------------------------
    | Manifests Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Returns the modules defined manifest properties.
     *
     * @param string $slug
     *
     * @return Collection
     */
    public function getManifest($slug);

    /*
    |--------------------------------------------------------------------------
    | Repository read and write Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Returns the given module property.
     *
     * @param string     $property
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get($property, $default = null);

    /**
     * Set the given module property value.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return bool
     */
    public function set($property, $value);

    /**
     * Returns the entire module repository content as a collection.
     *
     * @return Collection
     */
    public function load();

    /**
     * Saves the entire module content to the repository.
     *
     * @param $content
     * @return int|bool a non-zero or true value on success, or false on failure.
     */
    public function save($content);

    /*
    |--------------------------------------------------------------------------
    | Initialization Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Get all initialized modules.
     *
     * @return Collection
     */
    public function initialized();

    /**
     * Get all uninitialized modules.
     *
     * @return Collection
     */
    public function uninitialized();

    /**
     * Determines if the specified module is initialized.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isInitialized($slug);

    /**
     * Determines if the specified module is uninitialized.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isUninitialized($slug);

    /**
     * Initializes the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function initialize($slug);

    /**
     * Uninitializes the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function uninitialize($slug);

    /*
    |--------------------------------------------------------------------------
    | Enabling Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Get all enabled modules.
     *
     * @return Collection
     */
    public function enabled();

    /**
     * Get all disabled modules.
     *
     * @return Collection
     */
    public function disabled();

    /**
     * Determines if the specified module is enabled.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isEnabled($slug);

    /**
     * Determines if the specified module is disabled.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function isDisabled($slug);

    /**
     * Enables the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function enable($slug);

    /**
     * Disables the specified module.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function disable($slug);
}
