<?php

namespace Caffeinated\Modules\Tests\Commands\Commands;

use Caffeinated\Modules\Tests\BaseTestCase;

class CommandModuleDisableTest extends BaseTestCase
{
    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'disable', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_disable_an_enabled_module()
    {
        $cached = \Module::where('slug', 'disable');

        $this->assertTrue($cached->toArray()['enabled']);

        $this->artisan('module:disable', ['slug' => 'disable']);

        $cached = \Module::where('slug', 'disable');

        $this->assertFalse($cached->toArray()['enabled']);
    }

    /** @test */
    public function it_can_enable_a_disabled_module()
    {
        $this->artisan('module:disable', ['slug' => 'disable']);

        $cached = \Module::where('slug', 'disable');

        $this->assertFalse($cached->toArray()['enabled']);

        $this->artisan('module:enable', ['slug' => 'disable']);

        $cached = \Module::where('slug', 'disable');

        $this->assertTrue($cached->toArray()['enabled']);
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('disable'));

        parent::tearDown();
    }
}