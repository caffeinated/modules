<?php

namespace Caffeinated\Modules\Tests\Commands\Commands;

use Caffeinated\Modules\Tests\BaseTestCase;

class CommandModuleOptimizeTest extends BaseTestCase
{
    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'optimize', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_optimize_a_module()
    {
        $before = file_get_contents(storage_path('app/modules.json'));

        $this->assertSame(
            '{
    "Optimize": {
        "basename": "Optimize",
        "name": "Optimize",
        "slug": "optimize",
        "version": "1.0",
        "description": "This is the description for the Optimize module.",
        "id": 3797040228,
        "enabled": true,
        "order": 9001
    }
}',
            $before
        );

        //

        file_put_contents(module_path('optimize').'/module.json',
            json_encode(
                array_merge(
                    json_decode(file_get_contents(module_path('optimize').'/module.json'), true),
                    ['version' => '1.3.3.7']
                )
            , JSON_PRETTY_PRINT)
        );

        $this->artisan('module:optimize');

        //

        $optimized = file_get_contents(storage_path('app/modules.json'));

        $this->assertSame(
            '{
    "Optimize": {
        "basename": "Optimize",
        "name": "Optimize",
        "slug": "optimize",
        "version": "1.3.3.7",
        "description": "This is the description for the Optimize module.",
        "id": 3797040228,
        "enabled": true,
        "order": 9001
    }
}',
            $optimized
        );
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('optimize'));

        parent::tearDown();
    }
}