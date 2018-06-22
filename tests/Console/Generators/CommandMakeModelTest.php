<?php

namespace Caffeinated\Modules\Tests\Commands\Generators;

use Caffeinated\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandMakeModelTest extends BaseTestCase
{
    use MatchesSnapshots;

    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'model', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_generate_a_new_model_migration_with_default_module_namespace()
    {
        $this->artisan('make:module:model', ['slug' => 'model', 'name' => 'DefaultMigrationModel', '--migration' => 'migration']);

        $model = $this->finder->get(module_path('model') . '/Models/DefaultMigrationModel.php');

        $this->assertMatchesSnapshot($model);

        $files = $this->finder->allFiles(module_path('model') . '/Database/Migrations');
        $this->finder->move($files[0], module_path('model') . '/Database/Migrations/2018_06_18_000000_create_default_migration_models_table.php');

        $migration = $this->finder->get(module_path('model') . '/Database/Migrations/2018_06_18_000000_create_default_migration_models_table.php');

        $this->assertMatchesSnapshot($migration);
    }

    /** @test */
    public function it_can_generate_a_new_model_with_custom_module_namespace()
    {
        $this->app['config']->set('modules.namespace', 'App\\CustomModelNamespace\\');

        $this->artisan('make:module:model', ['slug' => 'model', 'name' => 'CustomModel']);

        $file = $this->finder->get(module_path('model') . '/Models/CustomModel.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_model_with_default_module_namespace()
    {
        $this->artisan('make:module:model', ['slug' => 'model', 'name' => 'DefaultModel']);

        $file = $this->finder->get(module_path('model') . '/Models/DefaultModel.php');

        $this->assertMatchesSnapshot($file);
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('model'));

        parent::tearDown();
    }
}