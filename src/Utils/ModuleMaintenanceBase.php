<?php

namespace Caffeinated\Modules\Utils;

use Caffeinated\Modules\Contracts\ModuleMaintenanceInterface;
use Caffeinated\Modules\Exceptions\ModuleDisablingFailureException;
use Caffeinated\Modules\Exceptions\ModuleEnablingFailureException;
use Caffeinated\Modules\Exceptions\ModuleInitializationFailureException;
use Caffeinated\Modules\Exceptions\ModuleUninitializationFailureException;
use File;
use Log;
use Module;

class ModuleMaintenanceBase implements ModuleMaintenanceInterface
{

    protected $slug;

    protected $moduleDef;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    public function __construct($slug)
    {
        $this->slug = $slug;
        $this->moduleDef = Module::where('slug', $slug);
    }

    /**
     * Determines if the module is initialized.
     *
     * @return bool
     */
    public function isInitialized()
    {
        return $this->moduleDef['initialized'] === true;
    }

    /**
     * Determines if the module is uninitialized.
     *
     * @return bool
     */
    public function isUninitialized()
    {
        return $this->moduleDef['initialized'] === false;
    }

    /**
     * Initialize the module.
     *
     * @return bool
     */
    public function initialize($func = null)
    {

        Log::debug('Inside ModuleMaintenanceBase initialize closure.');
        $rc = true;

        if ($this->isUninitialized()) {
            $isCallable = is_callable($func, false, $callable_name);
            if ($isCallable) {
                Log::debug('Calling higher closure from ModuleMaintenanceBase initialize closure.');
                $rc = call_user_func($func);

                // Call to publish any existing assets
                $this->publishAssets();
            }

            if ($rc) {
                event($this->slug.'.module.initialized', [$this->moduleDef, null]);
            } else {
                event($this->slug.'.module.failed.initialization', [$this->moduleDef, null]);
            }
        } else {
            throw new ModuleInitializationFailureException($this->slug, 'Cannot initialize a module already initialized.');
        }

        return $rc;

    }

    /**
     * Uninitialize the module.
     *
     * @return bool
     */
    public function uninitialize($func = null)
    {

        Log::debug('Inside ModuleMaintenanceBase uninitialize closure.');
        $rc = true;

        if ($this->isDisabled() && $this->isInitialized()) {
            $isCallable = is_callable($func, false, $callable_name);
            if ($isCallable) {
                Log::debug('Calling higher closure from ModuleMaintenanceBase uninitialize closure.');
                $rc = call_user_func($func);
            }

            if ($rc) {
                event($this->slug.'.module.uninitialized', [$this->moduleDef, null]);
            } else {
                event($this->slug.'.module.failed.uninitialization', [$this->moduleDef, null]);
            }
        } else {
            throw new ModuleUninitializationFailureException($this->slug, 'Cannot uninitialize a module either already uninitialized or enabled.');
        }

        return $rc;

    }

    /**
     * Determines if the module is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->moduleDef['enabled'] === true;
    }

    /**
     * Determines if the module is disabled.
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->moduleDef['enabled'] === false;
    }

    /**
     * Enables the module.
     *
     * @return bool
     */
    public function enable($func = null)
    {

        Log::debug('Inside ModuleMaintenanceBase enable closure.');
        $rc = true;

        if ($this->isDisabled() && $this->isInitialized()) {
            $isCallable = is_callable($func, false, $callable_name);
            if ($isCallable) {
                Log::debug('Calling higher closure from ModuleMaintenanceBase enable closure.');
                $rc = call_user_func($func);
            }

            if ($rc) {
                event($this->slug.'.module.enabled', [$this->moduleDef, null]);
            } else {
                event($this->slug.'.module.failed.enabling', [$this->moduleDef, null]);
            }
        } else {
            throw new ModuleEnablingFailureException($this->slug, 'Cannot enable a module either already enabled or uninitialized.');
        }

        return $rc;

    }

    /**
     * Disables the module.
     *
     * @return bool
     */
    public function disable($func = null)
    {

        Log::debug('Inside ModuleMaintenanceBase disable closure.');
        $rc = true;

        if ($this->isEnabled()) {
            $isCallable = is_callable($func, false, $callable_name);
            if ($isCallable) {
                Log::debug('Calling higher closure from ModuleMaintenanceBase disable closure.');
                $rc = call_user_func($func);
            }

            if ($rc) {
                event($this->slug.'.module.disabled', [$this->moduleDef, null]);
            } else {
                event($this->slug.'.module.failed.disabling', [$this->moduleDef, null]);
            }
        } else {
            throw new ModuleDisablingFailureException($this->slug, 'Cannot disable a module already disabled.');
        }

        return $rc;

    }

    /**
     * Allows a module to trigger the publishing of its assets.
     */
    public function publishAssets()
    {
        // Can't use the artisan publish command since the module is uninitialiazed & disabled the
        // Provider will not be registered and booted.
        // So we have to build our own deploy function.
        $assetsSource = config('modules.path') . '/' . $this->moduleDef['basename'] . '/assets';
        if (File::exists($assetsSource)) {
            $assetsTarget = config('modules.path_public_assets') . '/' . $this->slug;
            Log::debug('Copying module assets from [' . $assetsSource. '] to [' . $assetsTarget. '].');
            File::copyDirectory($assetsSource, $assetsTarget);
            event($this->slug.'.module.published-assets', [$this->moduleDef, null]);
        }
    }


}