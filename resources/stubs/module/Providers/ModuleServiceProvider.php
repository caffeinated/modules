<?php

namespace {{namespace}}\Providers;

use Caffeinated\Modules\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any module services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/Lang', '{{slug}}');
        $this->loadViewsFrom(__DIR__.'/../Resources/Views', '{{slug}}');
    }
}
