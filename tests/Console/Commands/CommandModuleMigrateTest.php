<?php

namespace Caffeinated\Modules\Tests\Commands\Commands;

use Caffeinated\Modules\Tests\BaseTestCase;

class CommandModuleMigrateTest extends BaseTestCase
{
    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'migrate', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_migrate_a_module()
    {
        $this->assertFalse(\Schema::hasTable('CustomCreateMigrationTable'));

        $this->artisan('make:module:migration', ['slug' => 'migrate', 'name' => 'CustomMigrate', '--create' => 'CustomCreateMigrationTable']);

        $this->artisan('module:migrate', ['slug' => 'migrate']);

        $this->assertTrue(\Schema::hasTable('CustomCreateMigrationTable'));
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('migrate'));

        parent::tearDown();
    }
}