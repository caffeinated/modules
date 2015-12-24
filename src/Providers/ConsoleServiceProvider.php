<?php
namespace Caffeinated\Modules\Providers;

use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
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
