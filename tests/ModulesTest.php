<?php

use Mockery as m;
use Caffeinated\Modules\Modules;

class ModulesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ModuleRepositoryInterface
     */
    protected $repository;

    /**
     * @var Modules
     */
    protected $module;

    /**
     * Set up test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->app = m::mock('Illuminate\Foundation\Application');
        $this->repository = m::mock('Caffeinated\Modules\Contracts\RepositoryInterface');
        $this->module = new Modules($this->app, $this->repository);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testHasCorrectInstance()
    {
        $this->assertInstanceOf('Caffeinated\Modules\Modules', $this->module);
    }
}
