<?php

namespace Caffeinated\Modules\Tests;

class TraitsTest extends BaseTestCase
{
    /** @test */
    public function it_can_check_if_dummy_model_with_trait_has_traits()
    {
        $with = new DummyModelWithTraits;

        $this->assertTrue(method_exists($with, 'requireMigrations'));

        $this->assertTrue(method_exists($with, 'getMigrationPath'));
    }

    /** @test */
    public function it_can_check_if_dummy_model_without_traits_has_no_traits()
    {
        $without = new DummyModelWithoutTraits;

        $this->assertFalse(method_exists($without, 'requireMigrations'));

        $this->assertFalse(method_exists($without, 'getMigrationPath'));
    }
}

class DummyModelWithTraits
{
    use \Caffeinated\Modules\Traits\MigrationTrait;
}

class DummyModelWithoutTraits
{
    //
}