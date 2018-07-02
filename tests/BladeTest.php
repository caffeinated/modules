<?php

namespace Caffeinated\Modules\Tests;

class BladeTest extends BaseTestCase
{
    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'blade', '--quick' => 'quick']);
    }

    /** @test */
    public function it_has_module_if_module_exists_and_is_enabled()
    {
        $this->artisan('module:enable', ['slug' => 'blade']);

        $this->assertEquals('has module', $this->renderView('module', ['module' => 'blade']));
    }

    /** @test */
    public function it_has_no_module_if_module_dont_exists()
    {
        $this->assertEquals('no module', $this->renderView('module', ['module' => 'dontexists']));
    }

    /** @test */
    public function it_has_no_module_if_module_exists_but_is_not_enabled()
    {
        $this->artisan('module:disable', ['slug' => 'blade']);

        $this->assertEquals('no module', $this->renderView('module', ['module' => 'blade']));
    }

    protected function renderView($view, $parameters)
    {
        $this->artisan('view:clear');

        return trim((string)(view($view)->with($parameters)));
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('blade'));

        parent::tearDown();
    }
}
