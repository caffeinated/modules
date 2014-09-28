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
		$this->registerMakeCommand();
		$this->registerEnableCommand();
		$this->registerDisableCommand();

		$this->commands([
			'modules.make',
			'modules.enable',
			'modules.disable'
		]);
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

	/**
	 * Register the "module:enable" console command.
	 *
	 * @return Console\ModuleEnableCommand
	 */
	protected function registerEnableCommand()
	{
		$this->app->bindShared('modules.enable', function($app) {
			return new Console\ModuleEnableCommand;
		});
	}

	/**
	 * Register the "module:disable" console command.
	 *
	 * @return Console\ModuleDisableCommand
	 */
	protected function registerDisableCommand()
	{
		$this->app->bindShared('modules.disable', function($app) {
			return new Console\ModuleDisableCommand;
		});
	}

	/**
	 * Register the "module:make" console command.
	 *
	 * @return Console\ModuleMakeCommand
	 */
	protected function registerMakeCommand()
	{
		$this->app->bindShared('modules.make', function($app) {
			$handler = new Handlers\ModuleMakeHandler($app['modules'], $app['files']);

			return new Console\ModuleMakeCommand($handler);
		});
	}
}
