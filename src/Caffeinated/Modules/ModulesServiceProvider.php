<?php
namespace Caffeinated\Modules;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('caffeinated/modules');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerServices();

		$this->registerConsoleCommands();
	}

	protected function registerServices()
	{
		$this->app->bindShared('modules.finder', function ($app) {
			return new Finder($app['files'], $app['config']);
		});

		$this->app->bindShared('modules', function ($app) {
			return new Modules(
				$app['modules.finder'],
				$app['config'],
				$app['view'],
				$app['translator'],
				$app['files'],
				$app['url']
			);
		});

		$this->app->booting(function ($app) {
			$app['modules']->register();
		});
	}

	protected function registerConsoleCommands()
	{
		// 
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['modules.finder', 'modules'];
	}

}
