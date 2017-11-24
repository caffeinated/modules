<?php

namespace Caffeinated\Modules\Contracts;

interface ModuleMaintenanceInterface
{

    public function __construct($slug);

    /**
     * Determines if the module is initialized.
     *
     * @return bool
     */
    public function isInitialized();

    /**
     * Determines if the module is uninitialized.
     *
     * @return bool
     */
    public function isUninitialized();

    /**
     * Initialize the module.
     *
     * @return bool
     */
    public function initialize($func = null);

    /**
     * Uninitialize the module.
     *
     * @return bool
     */
    public function uninitialize($func = null);

    /**
     * Determines if the module is enabled.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Determines if the module is disabled.
     *
     * @return bool
     */
    public function isDisabled();

    /**
     * Enables the module.
     *
     * @return bool
     */
    public function enable($func = null);

    /**
     * Disables the module.
     *
     * @return bool
     */
    public function disable($func = null);

    /**
     * Allows a module to trigger the publishing of its assets.
     *
     * @return mixed
     */
    public function publishAssets();

}