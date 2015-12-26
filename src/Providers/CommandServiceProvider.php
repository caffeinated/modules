<?php
namespace Caffeinated\Modules\Providers;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
	/**
     * Bootstrap the application services.
     *
     * @return void
     */
	public function boot()
	{
		//
	}

	/**
     * Register the application services.
     *
     * @return void
     */
	public function register()
	{
		$this->registerDisableCommand();
		$this->registerEnableCommand();
		$this->registerListCommand();
		$this->registerMigrateCommand();
	}

	/**
	 * Register the module:disable command.
	 *
	 * @return void
	 */
	protected function registerDisableCommand()
	{
		$this->app->singleton('command.module.disable', function() {
			return new \Caffeinated\Modules\Console\Commands\ModuleDisableCommand;
		});

		$this->commands('command.module.disable');
	}

	/**
	 * Register the module:enable command.
	 *
	 * @return void
	 */
	protected function registerEnableCommand()
	{
		$this->app->singleton('command.module.enable', function() {
			return new \Caffeinated\Modules\Console\Commands\ModuleEnableCommand;
		});

		$this->commands('command.module.enable');
	}

	/**
	 * Register the module:list command.
	 *
	 * @return void
	 */
	protected function registerListCommand()
	{
		$this->app->singleton('command.module.list', function($app) {
			return new \Caffeinated\Modules\Console\Commands\ModuleListCommand($app['modules']);
		});

		$this->commands('command.module.list');
	}

	/**
	 * Register the module:migrate command.
	 *
	 * @return void
	 */
	protected function registerMigrateCommand()
	{
		$this->app->singleton('command.module.migrate', function($app) {
			return new \Caffeinated\Modules\Console\Commands\ModuleMigrateCommand($app['migrator'], $app['modules']);
		});

		$this->commands('command.module.migrate');
	}
}
