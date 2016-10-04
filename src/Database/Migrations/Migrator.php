<?php

namespace Caffeinated\Modules\Database\Migrations;

use Illuminate\Support\Str;

class Migrator
{
    /**
     * @var Collection
     */
    protected $module;

    /**
     * The notes for the current operation.
     *
     * @var array
     */
    protected $notes = [];

    /**
     * Create a new Migrator instance.
     *
     * @param  Modules  $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * Rollback the last migration operation.
     *
     * @return array
     */
    public function rollback()
    {
        $this->notes = [];
        $rolledBack  = [];
        $migrations  = $this->getMigrations();
        $count       = count($migrations);

        if ($count === 0) {
            $this->note('<info>Nothing to rollback.</info>');
        } else {
            $this->requireMigrations();

            foreach ($migrations as $migration) {
                $rolledBack[] = $migration;

                $this->runDown($migration);
            }
        }

        return $rolledBack;
    }

    /**
     * Get migration directory path of the specified module.
     *
     * @param string $module
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        return module_path($this->module->get('slug'), 'Database/Migrations');
    }

    /**
     * Get all migrations for the specified module.
     *
     * @return array
     */
    protected function getMigrations()
    {
        $path = $this->getMigrationPath($this->module->get('slug'));

        return app()->make('files')->glob($path.'/*_*.php');
    }

    /**
     * Require (once) all migration files for the specified module.
     *
     * @param string $module
     */
    protected function requireMigrations()
    {
        $migrations = $this->getMigrations();

        foreach ($migrations as $migration) {
            app()->make('files')->requireOnce($migration);
        }
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string  $file
     * @return object
     */
    public function resolve($migration)
    {
        $class = Str::studly(implode('_', array_slice(explode('_', $migration), 4)));
        $class = str_replace('.php', '', $class);

        return app()->make($class);
    }

    /**
     * Run "down" a migration instance.
     *
     * @param  string  $migration
     * @param  boolean  $pretend
     * @return void
     */
    protected function runDown($migration, $pretend = false)
    {
        // Once we get an instance we can either run a pretend execution of the
        // migration or we can run the real migration.
        $instance = $this->resolve($migration);

        if ($pretend) {
            return $this->pretendToRun($instance, 'down');
        }

        $instance->down();

        // Once we have successfully run the migration "down" we will remove it from
        // the migration repository so it will be considered to have not been run
        // by the application then will be able to fire by any later operation.

        $where = explode('/', $migration);
        $where = end($where);
        $where = str_replace('.php', '', $where);

        $this->find($where)->delete();

        $this->note("<info>Rolled back:</info> {$where}");
    }

    /**
     * Get table instance.
     *
     * @return string
     */
    public function table()
    {
        return app()->make('db')->table(config('database.migrations'));
    }

    /**
     * Find migration data from database by given migration name.
     *
     * @param string $migration
     *
     * @return object
     */
    public function find($migration)
    {
        return $this->table()->whereMigration($migration);
    }

    /**
     * Raise a note event for the migrator.
     *
     * @param  string  $message
     * @return void
     */
    protected function note($message)
    {
        $this->notes[] = $message;
    }
}
