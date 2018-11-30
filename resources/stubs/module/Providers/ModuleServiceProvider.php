<?php

namespace DummyNamespace\Providers;

use Caffeinated\Modules\Support\ServiceProvider;

class DummyProvider extends ServiceProvider
{
    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(module_path('DummySlug', 'ResourcesLangMapping', 'DummyLocation'), 'DummySlug');
        $this->loadViewsFrom(module_path('DummySlug', 'ResourcesViewsMapping', 'DummyLocation'), 'DummySlug');
        $this->loadMigrationsFrom(module_path('DummySlug', 'DatabaseMigrationsMapping', 'DummyLocation'), 'DummySlug');
        $this->loadConfigsFrom(module_path('DummySlug', 'ConfigMapping', 'DummyLocation'));
        $this->loadFactoriesFrom(module_path('DummySlug', 'DatabaseFactoriesMapping', 'DummyLocation'));
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
