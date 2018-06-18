<?php

namespace Caffeinated\Modules\Tests;

use Caffeinated\Modules\Facades\Module;

class ModuleFacadeTest extends BaseTestCase
{
    /** @test */
    public function it_can_resolve_module_facade()
    {
        $modules = Module::all();

        $this->assertInstanceOf(\Countable::class, $modules);
    }
}