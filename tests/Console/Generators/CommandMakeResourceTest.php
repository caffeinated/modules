<?php

namespace Caffeinated\Modules\Tests\Commands\Generators;

use Caffeinated\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandMakeResourceTest extends BaseTestCase
{
    use MatchesSnapshots;

    protected $finder;

    public function setUp(): void
    {-
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'resource', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_generate_a_new_resource_with_default_module_namespace()
    {
        $this->artisan('make:module:resource', ['slug' => 'resource', 'name' => 'DefaultResource']);

        $file = $this->finder->get(module_path('resource') . '/Http/Resources/DefaultResource.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_resource_collection_with_default_module_namespace()
    {
        $this->artisan('make:module:resource', [
            'slug'         => 'resource',
            'name'         => 'DefaultResource',
            '--collection' => true,
        ]);

        $file = $this->finder->get(module_path('resource') . '/Http/Resources/DefaultResource.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_resource_with_custom_module_namespace()
    {
        $this->app['config']->set("modules.locations.$this->default.namespace", 'App\\CustomResourceNamespace\\');

        $this->artisan('make:module:resource', ['slug' => 'resource', 'name' => 'CustomResource']);

        $file = $this->finder->get(module_path('resource') . '/Http/Resources/CustomResource.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_resource_collection_with_custom_module_namespace()
    {
        $this->app['config']->set("modules.locations.$this->default.namespace", 'App\\CustomResourceNamespace\\');

        $this->artisan('make:module:resource', [
            'slug'         => 'resource',
            'name'         => 'CustomResource',
            '--collection' => true,
        ]);

        $file = $this->finder->get(module_path('resource') . '/Http/Resources/CustomResource.php');

        $this->assertMatchesSnapshot($file);
    }


    public function tearDown(): void
    {
        $this->finder->deleteDirectory(module_path('resource'));

        parent::tearDown();
    }
}