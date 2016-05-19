<?php

namespace Caffeinated\Modules\Console\Commands;

use App;
use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Arr;
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
     * @var Modules
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
     * @param Modules  $module
     */
    public function __construct(Migrator $migrator, Modules $module)
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
    public function fire()
    {
        $this->prepareDatabase();

        if (!empty($this->argument('slug'))) {
            $module = $this->module->where('slug', $this->argument('slug'))->first();

            if ($this->module->isEnabled($module['slug'])) {
                return $this->migrate($module['slug']);
            } elseif ($this->option('force')) {
                return $this->migrate($module['slug']);
            } else {
                return $this->error('Nothing to migrate.');
            }
        } else {
            if ($this->option('force')) {
                $modules = $this->module->all();
            } else {
                $modules = $this->module->enabled();
            }

            foreach ($modules as $module) {
                $this->migrate($module['slug']);
            }
        }
    }

    /**
     * Run migrations for the specified module.
     *
     * @param string $slug
     *
     * @return mixed
     */
    protected function migrate($slug)
    {
        if ($this->module->exists($slug)) {
            $pretend = Arr::get($this->option(), 'pretend', false);
            $path = $this->getMigrationPath($slug);

            if (floatval(App::version()) > 5.1) {
                $pretend = ['pretend' => $pretend];
            }

            $this->migrator->run($path, $pretend);

            // Once the migrator has run we will grab the note output and send it out to
            // the console screen, since the migrator itself functions without having
            // any instances of the OutputInterface contract passed into the class.
            foreach ($this->migrator->getNotes() as $note) {
                if (!$this->option('quiet')) {
                    $this->line($note);
                }
            }

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
        $path = $this->module->getModulePath($slug);

        return $path.'Database/Migrations/';
    }

    /**
     * Prepare the migration database for running.
     */
    protected function prepareDatabase()
    {
        $this->migrator->setConnection($this->option('database'));

        if (!$this->migrator->repositoryExists()) {
            $options = array('--database' => $this->option('database'));

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
        ];
    }
}
