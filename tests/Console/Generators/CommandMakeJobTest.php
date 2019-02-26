<?php

namespace Caffeinated\Modules\Tests\Commands\Generators;

use Caffeinated\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandMakeJobTest extends BaseTestCase
{
    use MatchesSnapshots;

    protected $finder;

    public function setUp(): void
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'jobs', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_generate_a_new_job_with_default_module_namespace()
    {
        $this->artisan('make:module:job', ['slug' => 'jobs', 'name' => 'DefaultJob']);

        $file = $this->finder->get(module_path('jobs').'/Jobs/DefaultJob.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_job_with_custom_module_namespace()
    {
        $this->app['config']->set("modules.locations.$this->default.namespace", 'App\\CustomJobsNamespace\\');

        $this->artisan('make:module:job', ['slug' => 'jobs', 'name' => 'CustomJob']);

        $file = $this->finder->get(module_path('jobs').'/Jobs/CustomJob.php');

        $this->assertMatchesSnapshot($file);
    }

    public function tearDown(): void
    {
        $this->finder->deleteDirectory(module_path('jobs'));

        parent::tearDown();
    }
}