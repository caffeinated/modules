<?php

namespace Caffeinated\Modules\Tests;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class BaseTestCase extends OrchestraTestCase
{
    public $default = 'app';

    public function setUp(): void
    {
        parent::setUp();

        if (File::isDirectory(module_path())) {
            File::deleteDirectory(module_path());
        }

        if (File::exists(storage_path('app/modules/modules.json'))) {
            File::delete(storage_path('app/modules/modules.json'));
        }
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Caffeinated\Modules\ModulesServiceProvider::class
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Module' => \Caffeinated\Modules\Facades\Module::class
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', array(
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ));

        $app['config']->set('view.paths', [__DIR__.'/resources/views']);

        $app['config']->set('modules.locations', [
            'app' => [
                'driver'    => 'local',
                'path'      => base_path('modules'),
                'namespace' => 'App\\Modules\\',
                'enabled'   => true,
                'provider'  => 'ModuleServiceProvider',
                'mapping'   => [],
                'manifest'  => 'module.json'
            ],
        ]);
    }
}