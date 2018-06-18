<?php

namespace Caffeinated\Modules\Tests\Commands\Generators;

use Caffeinated\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandMakeProviderTest extends BaseTestCase
{
    use MatchesSnapshots;

    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'provider', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_generate_a_new_provider_with_default_module_namespace()
    {
        $this->artisan('make:module:provider', ['slug' => 'provider', 'name' => 'DefaultProvider']);

        $file = $this->finder->get(module_path('provider').'/Providers/DefaultProvider.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_provider_with_custom_module_namespace()
    {
        $this->app['config']->set('modules.namespace', 'App\\CustomProviderNamespace\\');

        $this->artisan('make:module:provider', ['slug' => 'provider', 'name' => 'CustomProvider']);

        $file = $this->finder->get(module_path('provider').'/Providers/CustomProvider.php');

        $this->assertMatchesSnapshot($file);
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('provider'));

        parent::tearDown();
    }
}