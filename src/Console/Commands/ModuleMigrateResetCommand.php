<?php

namespace Caffeinated\Modules\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\ConfirmableTrait;
use Caffeinated\Modules\RepositoryManager;
use Illuminate\Database\Migrations\Migrator;
use Caffeinated\Modules\Repositories\Repository;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMigrateResetCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:migrate:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback all database migrations for a specific or all modules';

    /**
     * @var RepositoryManager
     */
    protected $module;

    /**
     * @var Migrator
     */
    protected $migrator;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param RepositoryManager    $module
     * @param Filesystem $files
     * @param Migrator   $migrator
     */
    public function __construct(RepositoryManager $module, Filesystem $files, Migrator $migrator)
    {
        parent::__construct();

        $this->module   = $module;
        $this->files    = $files;
        $this->migrator = $migrator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $repository = modules()->location($this->option('location'));

        $this->reset($repository);
    }

    /**
     * Run the migration reset for the current list of slugs.
     *
     * Migrations should be reset in the reverse order that they were
     * migrated up as. This ensures the database is properly reversed
     * without conflict.
     *
     * @param \Caffeinated\Modules\Repositories\Repository $repository
     *
     * @return mixed
     */
    protected function reset(Repository $repository)
    {
        $this->migrator->setconnection($this->input->getOption('database'));

        $migrationPaths = $this->getMigrationPaths($repository);
        $files = $this->migrator->setOutput($this->output)->getMigrationFiles($migrationPaths);

        $migrations = array_reverse($this->migrator->getRepository()->getRan());

        if (count($migrations) == 0) {
            $this->output->writeln("Nothing to rollback.");
        } else {
            $this->migrator->requireFiles($files);

            foreach ($migrations as $migration) {
                if (! array_key_exists($migration, $files)) {
                    continue;
                }

                $this->runDown($files[$migration], (object) ["migration" => $migration]);
            }
        }
    }

    /**
     * Run "down" a migration instance.
     *
     * @param string $slug
     * @param object $migration
     * @param bool   $pretend
     */
    protected function runDown($file, $migration)
    {
        $file     = $this->migrator->getMigrationName($file);
        $instance = $this->migrator->resolve($file);

        $instance->down();

        $this->migrator->getRepository()->delete($migration);

        $this->info("Rolledback: ".$file);
    }

    /**
     * Generate a list of all migration paths, given the arguments/operations supplied.
     *
     * @param \Caffeinated\Modules\Repositories\Repository $repository
     *
     * @return array
     */
    protected function getMigrationPaths(Repository $repository) {
        $migrationPaths = [];

        foreach ($this->getSlugsToReset($repository) as $slug) {
            $migrationPaths[] = $this->getMigrationPath($slug, $repository);

            event($slug.'.module.reset', [$this->module, $this->option()]);
        }

        return $migrationPaths;
    }

    /**
     * Using the arguments, generate a list of slugs to reset the migrations for.
     *
     * @param \Caffeinated\Modules\Repositories\Repository $repository
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getSlugsToReset(Repository $repository)
    {
        if ($this->validSlugProvided($repository)) {
            return collect([$this->argument('slug')]);
        }

        if ($this->option('force')) {
            return $repository->all()->pluck('slug');
        }

        return $repository->enabled()->pluck('slug');
    }

    /**
     * Determine if a valid slug has been provided as an argument.
     *
     * We will accept a slug as long as it is not empty and is enalbed (or force is passed).
     *
     * @param \Caffeinated\Modules\Repositories\Repository $repository
     *
     * @return bool
     */
    protected function validSlugProvided(Repository $repository)
    {
        if (empty($this->argument('slug'))) {
            return false;
        }

        if ($repository->isEnabled($this->argument('slug'))) {
            return true;
        }

        if ($this->option('force')) {
            return true;
        }

        return false;
    }

    /**
     * Get the console command parameters.
     * todo remove
     * @param string $slug
     *
     * @return array
     */
//    protected function getParameters($slug)
//    {
//        $params = [];
//
//        $params['--path'] = $this->getMigrationPath($slug);
//
//        if ($option = $this->option('database')) {
//            $params['--database'] = $option;
//        }
//
//        if ($option = $this->option('pretend')) {
//            $params['--pretend'] = $option;
//        }
//
//        if ($option = $this->option('seed')) {
//            $params['--seed'] = $option;
//        }
//
//        return $params;
//    }

    /**
     * Get migrations path.
     *
     * @param string $slug
     * @param \Caffeinated\Modules\Repositories\Repository $repository
     *
     * @return string
     */
    protected function getMigrationPath($slug, Repository $repository)
    {
        return module_path($slug, 'Database/Migrations', $repository->location);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [['slug', InputArgument::OPTIONAL, 'Module slug.']];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
            ['pretend', null, InputOption::VALUE_OPTIONAL, 'Dump the SQL queries that would be run.'],
            ['seed', null, InputOption::VALUE_OPTIONAL, 'Indicates if the seed task should be re-run.'],
            ['location', null, InputOption::VALUE_OPTIONAL, 'Which modules location to use.'],
        ];
    }
}
