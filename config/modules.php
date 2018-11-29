<?php

return [
    'default_location' => 'modules',

    'locations' => [
        'modules' => [
            'driver' => 'local',
            'path' => app_path('Modules'),
            'namespace' => 'App\\Modules\\',
            'enabled' => true,
            'provider' => 'Providers\\ModuleServiceProvider',
            'mapping' => [
                // To change where migrations go, specify the default
                // location as the key and the new location as the value:
                // 'Database/Migrations' => 'src/Database/Migrations',
            ],
            'manifest' => 'module.json'
        ],
    ],

    'default_driver' => 'local',

    /*
     |--------------------------------------------------------------------------
     | Module Drivers
     |--------------------------------------------------------------------------
     |
     | Here you may configure as many module drivers as you wish. Use the
     | local driver class as a basis for creating your own. The possibilities
     | are endless!
     |
     */

    'drivers' => [
        'local' => 'Caffeinated\Modules\Repositories\LocalRepository',
    ],
];
