<?php

namespace Caffeinated\Modules\Tests;

class MiddlewareTest extends BaseTestCase
{
    protected $finder;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->artisan('make:module', ['slug' => 'middleware', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_check_if_it_has_invalid_module_with_identify_module_middleware()
    {
        $this->app['router']->aliasMiddleware('module', \Caffeinated\Modules\Middleware\IdentifyModule::class);

        $this->app['router']->group(
            ['middleware' => [\Illuminate\Session\Middleware\StartSession::class, 'module:controller']],
            function () {
                $this->app['router']->get('has-invalid-identify-middleware', function () {
                    return session()->get('module');
                });
            }
        );

        $content = $this->call('get', 'has-invalid-identify-middleware')->getContent();

        $this->assertSame(
            '[]',
            $content
        );

        $this->assertSame(
            '[]',
            session()->get('module')->toJson()
        );
    }

    /** @test */
    public function it_can_check_if_it_has_no_identify_module_middleware()
    {
        $this->app['router']->get('has-no-identify-middleware', function () {
            return session()->get('module');
        });

        $content = $this->call('get', 'has-no-identify-middleware')->getContent();

        $this->assertSame('', $content);

        $this->assertFalse(session()->has('module'));
    }

    /** @test */
    public function it_can_check_if_it_has_valid_module_with_identify_module_middleware()
    {
        $this->app['router']->aliasMiddleware('module', \Caffeinated\Modules\Middleware\IdentifyModule::class);

        $this->app['router']->group(
            ['middleware' => [\Illuminate\Session\Middleware\StartSession::class, 'module:middleware']],
            function () {
                $this->app['router']->get('has-valid-identify-middleware', function () {
                    return session()->get('module');
                });
            }
        );

        $content = $this->call('get', 'has-valid-identify-middleware')->getContent();

        $this->assertSame(
            '{"basename":"Middleware","name":"Middleware","slug":"middleware","version":"1.0","description":"This is the description for the Middleware module.","id":2915276403,"enabled":true,"order":9001}',
            $content
        );

        $this->assertSame(
            '{"basename":"Middleware","name":"Middleware","slug":"middleware","version":"1.0","description":"This is the description for the Middleware module.","id":2915276403,"enabled":true,"order":9001}',
            session()->get('module')->toJson()
        );
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('middleware'));

        parent::tearDown();
    }
}