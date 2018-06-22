<?php

namespace Caffeinated\Modules\Tests\Commands\Commands;

use Caffeinated\Modules\Tests\BaseTestCase;

class CommandModuleMigrateResetTest extends BaseTestCase
{
    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'migrate-reset', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_migrate_reset_a_module()
    {
        $this->assertFalse(\Schema::hasTable('CustomCreateMigrationResetTable'));

        $this->artisan('make:module:migration', ['slug' => 'migrate-reset', 'name' => 'CustomMigrateReset', '--create' => 'CustomCreateMigrationResetTable']);

        $this->artisan('module:migrate', ['slug' => 'migrate-reset']);

        $this->assertTrue(\Schema::hasTable('CustomCreateMigrationResetTable'));

        $this->artisan('module:migrate:reset', ['slug' => 'migrate-reset']);

        $this->assertFalse(\Schema::hasTable('CustomCreateMigrationResetTable'));
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('migrate-reset'));

        parent::tearDown();
    }
}