<?php

namespace Caffeinated\Modules\Tests;

use Module;

class ModuleFacadeTest extends BaseTestCase
{
    /** @test */
    public function it_can_work_with_container()
    {
        $this->assertInstanceOf(\Caffeinated\Modules\Modules::class, $this->app['modules']);
    }

    /** @test */
    public function it_can_work_with_facade()
    {
        $this->assertSame('Caffeinated\Modules\Facades\Module', (new \ReflectionClass(Module::class))->getName());
    }
}