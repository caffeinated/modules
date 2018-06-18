<?php

namespace Caffeinated\Modules\Tests\Commands\Generators;

use Caffeinated\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandMakeRequestTest extends BaseTestCase
{
    use MatchesSnapshots;

    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'request', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_generate_a_new_request_with_default_module_namespace()
    {
        $this->artisan('make:module:request', ['slug' => 'request', 'name' => 'DefaultRequest']);

        $file = $this->finder->get(module_path('request').'/Http/Requests/DefaultRequest.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_request_with_custom_module_namespace()
    {
        $this->app['config']->set('modules.namespace', 'App\\CustomRequestNamespace\\');

        $this->artisan('make:module:request', ['slug' => 'request', 'name' => 'CustomRequest']);

        $file = $this->finder->get(module_path('request').'/Http/Requests/CustomRequest.php');

        $this->assertMatchesSnapshot($file);
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('request'));

        parent::tearDown();
    }
}