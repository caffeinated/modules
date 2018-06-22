<?php

namespace Caffeinated\Modules\Tests\Commands\Generators;

use Caffeinated\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandMakeMiddlewareTest extends BaseTestCase
{
    use MatchesSnapshots;

    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'middleware', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_generate_a_new_middleware_with_default_module_namespace()
    {
        $this->artisan('make:module:middleware', ['slug' => 'middleware', 'name' => 'DefaultMiddleware']);

        $file = $this->finder->get(module_path('middleware').'/Http/Middleware/DefaultMiddleware.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_middleware_with_custom_module_namespace()
    {
        $this->app['config']->set('modules.namespace', 'App\\MiddlewareModules\\');

        $this->artisan('make:module:middleware', ['slug' => 'middleware', 'name' => 'CustomMiddleware']);

        $file = $this->finder->get(module_path('middleware').'/Http/Middleware/CustomMiddleware.php');

        $this->assertMatchesSnapshot($file);
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('middleware'));

        parent::tearDown();
    }
}