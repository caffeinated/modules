<?php

namespace Caffeinated\Modules\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Caffeinated\Modules\RepositoryManager;
use Illuminate\Database\Migrations\Migrator;
use Caffeinated\Modules\Repositories\Repository;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMigrateCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the database migrations for a specific or all modules';

    /**
     * @var RepositoryManager
     */
    protected $module;

    /**
     * @var Migrator
     */
    protected $migrator;

    /**
     * Create a new command instance.
     *
     * @param Migrator $migrator
     * @param RepositoryManager  $module
     */
    public function __construct(Migrator $migrator, RepositoryManager $module)
    {
        parent::__construct();

        $this->migrator = $migrator;
        $this->module = $module;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->prepareDatabase();

        $repository = modules()->location($this->option('location'));

        $this->migrate($repository);
    }

    /**
     * @param \Caffeinated\Modules\Repositories\Repository $repository
     * @return mixed|void
     */
    protected function migrate(Repository $repository)
    {
        if (! empty($this->argument('slug'))) {
            $module = $repository->where('slug', $this->argument('slug'));

            if ($repository->isEnabled($module['slug'])) {
                $this->executeMigrations($module['slug'], $repository->location);
            } elseif ($this->option('force')) {
                $this->executeMigrations($module['slug'], $repository->location);
            }

            $this->error('Nothing to migrate.');
        } else {
            $modules = $this->option('force')
                ? $repository->all()
                : $repository->enabled();

            foreach ($modules as $module) {
                $this->executeMigrations($module['slug'], $repository->location);
            }
        }
    }

    /**
     * Run migrations for the specified module.
     *
     * @param string $slug
     * @param string $location
     *
     * @return mixed
     */
    protected function executeMigrations($slug, $location)
    {
        if (modules($location)->exists($slug)) {
            $module = modules($location)->where('slug', $slug);
            $pretend = Arr::get($this->option(), 'pretend', false);
            $step = Arr::get($this->option(), 'step', false);
            $path = $this->getMigrationPath($slug);

            $this->migrator->setOutput($this->output)->run($path, ['pretend' => $pretend, 'step' => $step]);

            event($slug.'.module.migrated', [$module, $this->option()]);

            // Finally, if the "seed" option has been given, we will re-run the database
            // seed task to re-populate the database, which is convenient when adding
            // a migration and a seed at the same time, as it is only this command.
            if ($this->option('seed')) {
                $this->call('module:seed', ['module' => $slug, '--force' => true]);
            }
        } else {
            return $this->error('Module does not exist.');
        }
    }

    /**
     * Get migration directory path.
     *
     * @param string $slug
     *
     * @return string
     */
    protected function getMigrationPath($slug)
    {
        return module_path($slug, 'Database/Migrations', $this->option('location'));
    }

    /**
     * Prepare the migration database for running.
     */
    protected function prepareDatabase()
    {
        $this->migrator->setConnection($this->option('database'));

        if (!$this->migrator->repositoryExists()) {
            $options = ['--database' => $this->option('database')];

            $this->call('migrate:install', $options);
        }
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
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually.'],
            ['location', null, InputOption::VALUE_OPTIONAL, 'Which modules location to use.'],
        ];
    }
}
