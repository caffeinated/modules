<?php

namespace Caffeinated\Modules\Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class LocationsTest extends BaseTestCase
{
    /**
     * @var \Caffeinated\Modules\Repositories\Repository
     */
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('modules.locations', [
            'app' => [
                'driver'    => 'local',
                'path'      => base_path('modules'),
                'namespace' => 'App\\Modules\\',
                'enabled'   => true
            ],
            'plugins' => [
                'driver' => 'local',
                'path' => base_path('plugins'),
                'namespace' => 'App\Plugins\\',
                'enabled' => true
            ]
        ]);

        $this->repository = modules('plugins');

        if (File::isDirectory(module_path(null, '', 'plugins'))) {
            File::deleteDirectory(module_path(null, '', 'plugins'));
        }

        if (File::exists(storage_path('app/modules/plugins.json'))) {
            File::delete(storage_path('app/modules/plugins.json'));
        }
    }

    /** @test */
    public function it_can_create_module_in_non_default_location()
    {
        $this->artisan('make:module', [
            'slug' => 'my-plugin',
            '--location' => 'plugins',
            '--quick' => true
        ]);

        $this->assertFileExists(base_path('plugins/MyPlugin/module.json'));

        $this->assertFileNotExists(base_path('modules/MyPlugin/module.json'));
    }

    /** @test */
    public function it_can_disable_and_enable_module_in_non_default_location()
    {
        $this->artisan('make:module', [
            'slug' => 'foo-bar',
            '--location' => 'plugins',
            '--quick' => true
        ]);

        $this->artisan('make:module:migration', [
            'slug' => 'foo-bar',
            'name' => 'create_foo_bar_plugin_table',
            '--create' => 'foo_bar_plugin',
            '--location' => 'plugins'
        ]);

        $this->artisan('module:disable', [
            'slug' => 'foo-bar',
            '--location' => 'plugins'
        ]);

        $this->assertTrue($this->repository->isDisabled('foo-bar'));

        $this->artisan('module:enable', [
            'slug' => 'foo-bar',
            '--location' => 'plugins'
        ]);

        $this->assertTrue($this->repository->isEnabled('foo-bar'));
    }

    /** @test */
    public function it_can_work_with_migrations_in_non_default_location()
    {
        $this->artisan('make:module', [
            'slug' => 'bar-biz',
            '--location' => 'plugins',
            '--quick' => true
        ]);

        $this->artisan('make:module:migration', [
            'slug' => 'bar-biz',
            'name' => 'create_bar_biz_plugin_table',
            '--create' => 'bar_biz_plugin',
            '--location' => 'plugins'
        ]);

        $this->artisan('module:migrate', [
            '--location' => 'plugins'
        ]);
        $this->assertTrue(Schema::hasTable('bar_biz_plugin'));

        //

        $this->artisan('module:migrate:rollback', [
            '--location' => 'plugins'
        ]);
        $this->assertFalse(Schema::hasTable('bar_biz_plugin'));

        //

        $this->artisan('module:migrate:refresh', [
            '--location' => 'plugins'
        ]);
        $this->assertTrue(Schema::hasTable('bar_biz_plugin'));

        //

        $this->artisan('module:migrate:reset', [
            '--location' => 'plugins'
        ]);
        $this->assertFalse(Schema::hasTable('bar_biz_plugin'));
    }

    /** @test */
    public function it_should_leave_default_location_alone_when_working_with_migrations_in_non_default_location()
    {
        // create a module for the *default* location to make
        // sure that when a location is specified, that
        // it leaves the default location alone
        $this->artisan('make:module', [
            'slug' => 'module-in-default-location',
            '--quick' => true
        ]);

        $this->artisan('make:module:migration', [
            'slug' => 'module-in-default-location',
            'name' => 'should_not_create_migration',
            '--create' => 'default_location_table',
        ]);

        //

        $this->artisan('module:migrate', [
            '--location' => 'plugins'
        ]);
        $this->assertFalse(Schema::hasTable('default_location_table'));

        //

        // actually run the default location migration to test to make sure
        // the *rollback* for a non-default location leaves the default
        // location migrations alone.
        $this->artisan('module:migrate');
        $this->assertTrue(Schema::hasTable('default_location_table'));

        //

        // test that the default location migrations were left alone.
        $this->artisan('module:migrate:rollback', [
            '--location' => 'plugins'
        ]);
        $this->assertTrue(Schema::hasTable('default_location_table'));

        // actually roll that migration back now for next test
        $this->artisan('module:migrate:rollback');
        $this->assertFalse(Schema::hasTable('default_location_table'));

        //

        $this->artisan('module:migrate:refresh', [
            '--location' => 'plugins'
        ]);
        $this->assertFalse(Schema::hasTable('default_location_table'));

        //

        $this->artisan('module:migrate');
        $this->artisan('module:migrate:reset', [
            '--location' => 'plugins'
        ]);
        $this->assertTrue(Schema::hasTable('default_location_table'));
    }

    /** @test */
    public function it_leaves_non_default_locations_alone_when_working_with_default_location()
    {
        $this->artisan('make:module', [
            'slug' => 'baz-biz',
            '--location' => 'plugins',
            '--quick' => true
        ]);

        $this->artisan('make:module:migration', [
            'slug' => 'baz-biz',
            'name' => 'create_baz_biz_plugin_table',
            '--create' => 'baz_biz_plugin',
            '--location' => 'plugins'
        ]);

        $this->artisan('module:migrate');
        $this->assertFalse(Schema::hasTable('baz_biz_plugin'));

        //

        $this->artisan('module:migrate', [
            '--location' => 'plugins',
        ]);

        $this->assertTrue(Schema::hasTable('baz_biz_plugin'));
        $this->artisan('module:migrate:rollback'); // no specified location
        $this->assertTrue(Schema::hasTable('baz_biz_plugin'));

        //

        $this->artisan('module:migrate:reset'); // no specified location
        $this->assertTrue(Schema::hasTable('baz_biz_plugin'));
    }
}
