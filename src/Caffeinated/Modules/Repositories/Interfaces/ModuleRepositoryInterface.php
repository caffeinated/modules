<?php
namespace Caffeinated\Modules\Repositories\Interfaces;

interface ModuleRepositoryInterface
{
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
	 * Determines if the given module exists.
	 *
	 * @param  string  $slug
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
	 * Sort modules by the given property key.
	 *
	 * @param  string  $key
	 * @return Collection
	 */
	public function sortBy($key);

	/**
	 * Returns the modules defined properties.
	 *
	 * @param  string  $slug
	 * @return Collection
	 */
	public function getProperties($slug);

	/**
	 * Returns the given module property.
	 *
	 * @param  string       $property
	 * @param  mixed|null   $default
	 * @return mixed|null
	 */
	public function getProperty($property, $default = null);

	/**
	 * Set the given module property value.
	 *
	 * @param  string  $property
	 * @param  mixed   $value
	 * @return bool
	 */
	public function setProperty($property, $value);

	/**
	 * Get all modules by their enabled status.
	 *
	 * @param  bool  $enabled
	 * @return Collection
	 */
	public function getByEnabled($enabled = true);

	/**
	 * Alias method for getByEnabled(true).
	 *
	 * @return Collection
	 */
	public function enabled();

	/**
	 * Alias method for getByEnabled(false).
	 *
	 * @return Collection
	 */
	public function disabled();

	/**
	 * Determines if the specified module is enabled.
	 *
	 * @param  string  $slug
	 * @return bool
	 */
	public function isEnabled($slug);

	/**
	* Determines if the specified module is disabled.
	*
	* @param  string  $slug
	* @return bool
	*/
	public function isDisabled();

	/**
	 * Enables the specified module.
	 *
	 * @param  string  $slug
	 * @return bool
	 */
	public function enable($slug);

	/**
	* Disables the specified module.
	*
	* @param  string  $slug
	* @return bool
	*/
	public function disable($slug);
}
