<?php

namespace Caffeinated\Modules\Tests\Commands\Generators;

use Caffeinated\Modules\Tests\BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandMakeSeederTest extends BaseTestCase
{
    use MatchesSnapshots;

    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'seeder', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_generate_a_new_seeder_with_default_module_namespace()
    {
        $this->artisan('make:module:seeder', ['slug' => 'seeder', 'name' => 'DefaultSeeder']);

        $file = $this->finder->get(module_path('seeder').'/Database/Seeds/DefaultSeeder.php');

        $this->assertMatchesSnapshot($file);
    }

    /** @test */
    public function it_can_generate_a_new_seeder_with_custom_module_namespace()
    {
        $this->app['config']->set('modules.namespace', 'App\\CustomSeederNamespace\\');

        $this->artisan('make:module:seeder', ['slug' => 'seeder', 'name' => 'CustomSeeder']);

        $file = $this->finder->get(module_path('seeder').'/Database/Seeds/CustomSeeder.php');

        $this->assertMatchesSnapshot($file);
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('seeder'));

        parent::tearDown();
    }
}