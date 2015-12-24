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
		$this->registerEnableCommand();
		$this->registerDisableCommand();
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
}
