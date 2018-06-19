<?php

namespace Caffeinated\Modules\Tests\Commands\Commands;

use Caffeinated\Modules\Tests\BaseTestCase;

class CommandModuleMigrateRollbackTest extends BaseTestCase
{
    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'migrate-rollback', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_migrate_rollback_a_module()
    {
        $this->assertFalse(\Schema::hasTable('CustomCreateMigrationRollbackTable'));

        $this->artisan('make:module:migration', ['slug' => 'migrate-rollback', 'name' => 'CustomMigrateRollback', '--create' => 'CustomCreateMigrationRollbackTable']);

        $this->artisan('module:migrate', ['slug' => 'migrate-rollback']);

        $this->assertTrue(\Schema::hasTable('CustomCreateMigrationRollbackTable'));

        $this->artisan('module:migrate:rollback', ['slug' => 'migrate-rollback']);

        $this->assertFalse(\Schema::hasTable('CustomCreateMigrationRollbackTable'));
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('migrate-rollback'));

        parent::tearDown();
    }
}