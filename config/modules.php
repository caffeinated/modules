<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Path to Modules
    |--------------------------------------------------------------------------
    |
    | Define the path where you'd like to store your modules. Note that if you
    | choose a path that's outside of your public directory, you will need to
    | copy your module assets (CSS, images, etc.) to your public directory.
    |
    */

    'path' => app_path('Modules'),

    /*
    |--------------------------------------------------------------------------
    | Modules Default State
    |--------------------------------------------------------------------------
    |
    | When a previously unknown module is added, if it doesn't have an 'enabled'
    | state set then this is the value which it will default to. If this is
    | not provided then the module will default to being 'enabled'.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Modules Base Namespace
    |--------------------------------------------------------------------------
    |
    | Define the base namespace for your modules. Be sure to update this value
    | if you move your modules directory to a new path. This is primarily used
    | by the module:make Artisan command.
    |
    */

    'namespace' => 'App\Modules\\',

    /*
    |--------------------------------------------------------------------------
    | Default Module Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the module storage driver that will be utilized.
    | This driver manages the retrieval and management of module properties.
    | Setting this to custom allows you to specify your own driver instance.
    |
    | Supported: "local", "custom"
    |
    */

    'driver' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Custom Module Driver
    |--------------------------------------------------------------------------
    |
    | This option allows one to define a custom module driver implementation.
    | This is useful in cases where you may need to support and store module
    | properties somewhere not supported by default.
    |
    */

    'custom_driver' => 'App\Repositories\Modules\CustomRepository',

    /*
    |--------------------------------------------------------------------------
    | Custom Stubs
    |--------------------------------------------------------------------------
    |
    | This option allows to define a custom stubs.
    | php artisan vendor:publish --tag=stubs
    |
    */
    //'custom_stubs' => base_path('resources\stubs\\'),
];
