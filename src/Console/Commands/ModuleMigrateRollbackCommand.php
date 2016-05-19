<?php

namespace Caffeinated\Modules\Console\Commands;

use Caffeinated\Modules\Modules;
use Caffeinated\Modules\Traits\MigrationTrait;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMigrateRollbackCommand extends Command
{
    use MigrationTrait, ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:migrate:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last database migrations for a specific or all modules';

    /**
     * @var Modules
     */
    protected $module;

    /**
     * Create a new command instance.
     *
     * @param Modules $module
     */
    public function __construct(Modules $module)
    {
        parent::__construct();

        $this->module = $module;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $slug = $this->argument('slug');

        if ($slug) {
            return $this->rollback($slug);
        } else {
            foreach ($this->module->all() as $module) {
                $this->rollback($module['slug']);
            }
        }
    }

    /**
     * Run the migration rollback for the specified module.
     *
     * @param string $slug
     *
     * @return mixed
     */
    protected function rollback($slug)
    {
        $this->requireMigrations($slug);

        $this->call('migrate:rollback', [
            '--database' => $this->option('database'),
            '--force' => $this->option('force'),
            '--pretend' => $this->option('pretend'),
        ]);
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
        ];
    }
}
