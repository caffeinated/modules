<?php

namespace Caffeinated\Modules\Tests\Commands\Generators;

use Caffeinated\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandMakeMigrationTest extends BaseTestCase
{
    use MatchesSnapshots;

    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'migration', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_generate_a_new_migration_with_custom_module_namespace()
    {
        $this->artisan('make:module:migration', ['slug' => 'migration', 'name' => 'CustomMigration']);

        $files = $this->finder->allFiles(module_path('migration') . '/Database/Migrations');
        $this->finder->move($files[0], module_path('migration') . '/Database/Migrations/2018_06_18_000000_create_custom_migration_models_table.php');

        $migration = $this->finder->get(module_path('migration') . '/Database/Migrations/2018_06_18_000000_create_custom_migration_models_table.php');

        $this->assertMatchesSnapshot($migration);
    }

    /** @test */
    public function it_can_generate_a_new_migration_with_default_module_namespace()
    {
        $this->artisan('make:module:migration', ['slug' => 'migration', 'name' => 'DefaultMigration']);

        $files = $this->finder->allFiles(module_path('migration') . '/Database/Migrations');
        $this->finder->move($files[0], module_path('migration') . '/Database/Migrations/2018_06_18_000000_create_default_migration_models_table.php');

        $migration = $this->finder->get(module_path('migration') . '/Database/Migrations/2018_06_18_000000_create_default_migration_models_table.php');

        $this->assertMatchesSnapshot($migration);
    }

    /** @test */
    public function it_can_generate_a_new_migration_with_table_create()
    {
        $this->artisan('make:module:migration', ['slug' => 'migration', 'name' => 'CustomMigration', '--create' => 'CustomCreateMigrationTable']);

        $files = $this->finder->allFiles(module_path('migration') . '/Database/Migrations');
        $this->finder->move($files[0], module_path('migration') . '/Database/Migrations/2018_06_18_000000_create_custom_migration_models_table.php');

        $migration = $this->finder->get(module_path('migration') . '/Database/Migrations/2018_06_18_000000_create_custom_migration_models_table.php');

        $this->assertMatchesSnapshot($migration);
    }

    /** @test */
    public function it_can_generate_a_new_migration_with_table_create_and_migrate()
    {
        $this->artisan('make:module:migration', ['slug' => 'migration', 'name' => 'CustomMigration', '--create' => 'CustomCreateMigrationTable', '--table' => 'CustomTableMigrationTable']);

        $files = $this->finder->allFiles(module_path('migration') . '/Database/Migrations');
        $this->finder->move($files[0], module_path('migration') . '/Database/Migrations/2018_06_18_000000_create_custom_migration_models_table.php');

        $migration = $this->finder->get(module_path('migration') . '/Database/Migrations/2018_06_18_000000_create_custom_migration_models_table.php');

        $this->assertMatchesSnapshot($migration);
    }

    /** @test */
    public function it_can_generate_a_new_migration_with_table_migrate()
    {
        $this->artisan('make:module:migration', ['slug' => 'migration', 'name' => 'CustomMigration', '--table' => 'CustomTableMigrationTable']);

        $files = $this->finder->allFiles(module_path('migration') . '/Database/Migrations');
        $this->finder->move($files[0], module_path('migration') . '/Database/Migrations/2018_06_18_000000_create_custom_migration_models_table.php');

        $migration = $this->finder->get(module_path('migration') . '/Database/Migrations/2018_06_18_000000_create_custom_migration_models_table.php');

        $this->assertMatchesSnapshot($migration);
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('migration'));

        parent::tearDown();
    }
}