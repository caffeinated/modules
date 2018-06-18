<?php

namespace Caffeinated\Modules\Tests\Commands\Generators;

use Caffeinated\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandMakeTestTest extends BaseTestCase
{
    use MatchesSnapshots;

    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'test', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_generate_a_new_test_with_default_module_namespace()
    {
        $this->artisan('make:module:test', ['slug' => 'test', 'name' => 'DefaultTest']);

        $file = $this->finder->get(module_path('test').'/Tests/DefaultTest.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_test_with_custom_module_namespace()
    {
        $this->app['config']->set('modules.namespace', 'App\\CustomTestNamespace\\');

        $this->artisan('make:module:test', ['slug' => 'test', 'name' => 'CustomTest']);

        $file = $this->finder->get(module_path('test').'/Tests/CustomTest.php');

        $this->assertMatchesSnapshot($file);
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('test'));

        parent::tearDown();
    }
}