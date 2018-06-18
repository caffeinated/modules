<?php

namespace Caffeinated\Modules\Tests;

class HelpersTest extends BaseTestCase
{
    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'helper', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_get_module_path()
    {
        $this->assertContains('modules/Helper', module_path('helper'));
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('helper'));

        parent::tearDown();
    }
}