<?php

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

		$this->handler = Mockery::mock('Caffeinated\Modules\Handlers\ModulesHandler');
		$this->config  = Mockery::mock('Illuminate\Config\Repository');
		$this->files   = Mockery::mock('Illuminate\Filesystem\Filesystem');
		$this->module  = new Modules($this->handler, $this->config, $this->files);
	}

	public function tearDown()
	{
		Mockery::close();
	}

	/** @test */
	public function hasCorrectInstance()
	{
		$this->assertInstanceOf('Caffeinated\Modules\Modules', $this->module);
	}

	/** @test */
	public function getsAllModules()
	{
		$this->handler->shouldReceive('all')->once();

		$modules = $this->module->all();

		$this->assertInstanceOf(
			'Illuminate\Database\Eloquent\Collection',
			$modules
		);
	}
}
