<?php

namespace Caffeinated\Modules\Traits;

trait MigrationTrait
{
	/**
	 * Require (once) all migration files for the supplied module.
	 *
	 * @param string $module
	 * @return void
	 */
	protected function requireMigrations($module)
	{
		$path = $this->laravel['modules']->getModulePath($module).'Database/Migrations/';
		
		$migrations = $this->laravel['files']->glob($path.'/*_*.php');

		foreach ($migrations as $migration) {
			$this->laravel['files']->requireOnce($migration);
		}
	}
}