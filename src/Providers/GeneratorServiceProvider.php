<?php

namespace Caffeinated\Modules\Providers;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the provided services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the provided services.
     */
    public function register()
    {
        $generators = [
            'command.make.module'            => \Caffeinated\Modules\Console\Generators\MakeModuleCommand::class,
            'command.make.module.controller' => \Caffeinated\Modules\Console\Generators\MakeControllerCommand::class,
            'command.make.module.middleware' => \Caffeinated\Modules\Console\Generators\MakeMiddlewareCommand::class,
            'command.make.module.migration'  => \Caffeinated\Modules\Console\Generators\MakeMigrationCommand::class,
            'command.make.module.model'      => \Caffeinated\Modules\Console\Generators\MakeModelCommand::class,
            'command.make.module.policy'     => \Caffeinated\Modules\Console\Generators\MakePolicyCommand::class,
            'command.make.module.provider'   => \Caffeinated\Modules\Console\Generators\MakeProviderCommand::class,
            'command.make.module.request'    => \Caffeinated\Modules\Console\Generators\MakeRequestCommand::class,
            'command.make.module.resource'   => \Caffeinated\Modules\Console\Generators\MakeResourceCommand::class,
            'command.make.module.seeder'     => \Caffeinated\Modules\Console\Generators\MakeSeederCommand::class,
            'command.make.module.test'       => \Caffeinated\Modules\Console\Generators\MakeTestCommand::class,
            'command.make.module.job'        => \Caffeinated\Modules\Console\Generators\MakeJobCommand::class,
        ];

        foreach ($generators as $slug => $class) {
            $this->app->singleton($slug, function ($app) use ($slug, $class) {
                return $app[$class];
            });

            $this->commands($slug);
        }
    }
}
