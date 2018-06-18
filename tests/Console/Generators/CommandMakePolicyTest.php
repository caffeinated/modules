<?php

namespace Caffeinated\Modules\Tests\Commands\Generators;

use Caffeinated\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandMakePolicyTest extends BaseTestCase
{
    use MatchesSnapshots;

    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'policy', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_generate_a_new_policy_with_default_module_namespace()
    {
        $this->artisan('make:module:policy', ['slug' => 'policy', 'name' => 'DefaultPolicy']);

        $file = $this->finder->get(module_path('policy').'/Policies/DefaultPolicy.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_policy_with_custom_module_namespace()
    {
        $this->app['config']->set('modules.namespace', 'App\\CustomPolicyNamespace\\');

        $this->artisan('make:module:policy', ['slug' => 'policy', 'name' => 'CustomPolicy']);

        $file = $this->finder->get(module_path('policy').'/Policies/CustomPolicy.php');

        $this->assertMatchesSnapshot($file);
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('policy'));

        parent::tearDown();
    }
}