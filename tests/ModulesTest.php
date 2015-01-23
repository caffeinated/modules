<?php

use Mockery as m;
use Caffeinated\Modules\Modules;
use Illuminate\Database\Eloquent\Collection;

class ModulesTest extends PHPUnit_Framework_TestCase
{
	protected $handler;

	protected $config;

	protected $files;

	protected $module;

	public function setUp()
	{
		parent::setUp();

		$this->handler = m::mock('Caffeinated\Modules\Handlers\ModulesHandler');
		$this->config  = m::mock('Illuminate\Config\Repository');
		$this->files   = m::mock('Illuminate\Filesystem\Filesystem');
		$this->module  = new Modules($this->handler, $this->config, $this->files);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testHasCorrectInstance()
	{
		$this->assertInstanceOf('Caffeinated\Modules\Modules', $this->module);
	}

	public function testAllModules()
	{
		$this->handler->shouldReceive('all')->once();

		$modules = $this->module->all();

		$this->assertInstanceOf(
			'Illuminate\Database\Eloquent\Collection',
			$modules
		);
	}
}
