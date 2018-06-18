<?php

namespace Caffeinated\Modules\Tests;

use Illuminate\Support\Collection;

class RepositoryTest extends BaseTestCase
{
    protected $finder;

    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->finder = $this->app['files'];

        $this->repository = new \Caffeinated\Modules\Modules(
            $this->app,
            $this->app->make(\Caffeinated\Modules\Contracts\Repository::class)
        );

        $this->artisan('make:module', ['slug' => 'RepositoryMod2', '--quick' => 'quick']);

        $this->artisan('make:module', ['slug' => 'RepositoryMod1', '--quick' => 'quick']);

        $this->artisan('make:module', ['slug' => 'RepositoryMod3', '--quick' => 'quick']);
    }

    /** @test */
    public function it_can_get_all_the_modules()
    {
        $this->assertCount(3, $this->repository->all());

        $this->assertInstanceOf(Collection::class, $this->repository->all());
    }

    /** @test */
    public function it_can_get_the_modules_slugs()
    {
        $this->assertCount(3, $this->repository->slugs());

        $this->repository->slugs()->each(function($key, $value) {
            $this->assertSame('repositorymod'.($value+1), $key);
        });
    }

    /** @test */
    public function it_can_get_a_module_based_on_where()
    {
        $slug = $this->repository->where('slug', 'repositorymod1');

        $this->assertInstanceOf(Collection::class, $slug);

        $this->assertCount(8, $slug);

        //

        $basename = $this->repository->where('basename', 'Repositorymod1');

        $this->assertInstanceOf(Collection::class, $basename);

        $this->assertCount(8, $basename);

        //

        $name = $this->repository->where('name', 'Repositorymod1');

        $this->assertInstanceOf(Collection::class, $name);

        $this->assertCount(8, $name);
    }

    /** @test */
    public function it_can_sortby_asc_slug_the_modules()
    {
        $sortByAsc = array_keys($this->repository->sortby('slug')->toArray());

        $this->assertSame($sortByAsc[0], 'Repositorymod1');
        $this->assertSame($sortByAsc[1], 'Repositorymod2');
        $this->assertSame($sortByAsc[2], 'Repositorymod3');
    }

    /** @test */
    public function it_can_sortby_desc_slug_the_modules()
    {
        $sortByAsc = array_keys($this->repository->sortbyDesc('slug')->toArray());

        $this->assertSame($sortByAsc[0], 'Repositorymod3');
        $this->assertSame($sortByAsc[1], 'Repositorymod2');
        $this->assertSame($sortByAsc[2], 'Repositorymod1');
    }

    /** @test */
    public function it_can_check_if_the_module_exists()
    {
        $this->assertTrue($this->repository->exists('repositorymod1'));

        $this->assertFalse($this->repository->exists('repositorymod4'));
    }

    /** @test */
    public function it_can_count_the_modules()
    {
        $this->assertSame(3, (int) $this->repository->count());
    }

    /** @test */
    public function it_can_get_manifest_of_module()
    {
        $manifest = $this->repository->getManifest('repositorymod1');

        $this->assertSame(
            '{"name":"Repositorymod1","slug":"repositorymod1","version":"1.0","description":"This is the description for the Repositorymod1 module."}',
            $manifest->toJson()
        );
    }

    /** @test */
    public function it_can_get_property_of_module()
    {
        $this->assertSame('Repositorymod1', $this->repository->get('repositorymod1::name'));

        $this->assertSame('1.0', $this->repository->get('repositorymod2::version'));

        $this->assertSame('This is the description for the Repositorymod3 module.', $this->repository->get('repositorymod3::description'));
    }

    /** @test */
    public function it_can_set_property_of_module()
    {
        $this->assertSame('Repositorymod1', $this->repository->get('repositorymod1::name'));

        $this->repository->set('repositorymod1::name', 'FooBarRepositorymod1');

        $this->assertSame('FooBarRepositorymod1', $this->repository->get('repositorymod1::name'));

        //

        $this->assertSame('1.0', $this->repository->get('repositorymod3::version'));

        $this->repository->set('repositorymod3::version', '1.3.3.7');

        $this->assertSame('1.3.3.7', $this->repository->get('repositorymod3::version'));
    }

    /** @test */
    public function it_can_get_a_collection_of_enabled_modules()
    {
        $this->assertSame(3, (int) $this->repository->enabled()->count());

        $this->repository->disable('repositorymod1');

        $this->assertSame(2, (int) $this->repository->enabled()->count());
    }

    /** @test */
    public function it_can_get_a_collection_of_disabled_modules()
    {
        $this->assertSame(0, (int) $this->repository->disabled()->count());

        $this->repository->disable('repositorymod1');

        $this->assertSame(1, (int) $this->repository->disabled()->count());
    }

    /** @test */
    public function it_can_check_if_module_is_enabled()
    {
        $this->assertTrue($this->repository->isEnabled('repositorymod1'));

        $this->repository->disable('repositorymod1');

        $this->assertFalse($this->repository->isEnabled('repositorymod1'));
    }

    /** @test */
    public function it_can_check_if_module_is_disabled()
    {
        $this->assertFalse($this->repository->isDisabled('repositorymod1'));

        $this->repository->disable('repositorymod1');

        $this->assertTrue($this->repository->isDisabled('repositorymod1'));
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory(module_path('repositorymod1'));

        $this->finder->deleteDirectory(module_path('repositorymod2'));

        $this->finder->deleteDirectory(module_path('repositorymod3'));

        parent::tearDown();
    }
}