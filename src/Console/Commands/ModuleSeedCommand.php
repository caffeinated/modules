<?php

namespace Caffeinated\Modules\Console\Commands;

use Illuminate\Console\Command;
use Caffeinated\Modules\RepositoryManager;
use Caffeinated\Modules\Repositories\Repository;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleSeedCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with records for a specific or all modules';

    /**
     * @var RepositoryManager
     */
    protected $module;

    /**
     * Create a new command instance.
     *
     * @param RepositoryManager $module
     */
    public function __construct(RepositoryManager $module)
    {
        parent::__construct();

        $this->module = $module;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $repository = modules($this->option('location') ?? config('modules.default_location'));

        $slug = $this->argument('slug');

        if ($slug) {
            if (! $repository->exists($slug)) {
                return $this->error('Module does not exist.');
            }

            if ($repository->isEnabled($slug)) {
                $this->seed($slug, $repository);
            }
            elseif ($this->option('force')) {
                $this->seed($slug, $repository);
            }

            return;
        }
        else {
            if ($this->option('force')) {
                $modules = $repository->all();
            }
            else {
                $modules = $repository->enabled();
            }

            foreach ($modules as $module) {
                $this->seed($module['slug'], $repository);
            }
        }
    }

    /**
     * Seed the specific module.
     *
     * @param string $slug
     * @param \Caffeinated\Modules\Repositories\Repository $repository
     *
     * @return void
     */
    protected function seed($slug, Repository $repository)
    {
        $module        = $repository->where('slug', $slug);
        $params        = [];
        $namespacePath = $repository->getNamespace();
        $rootSeeder    = $module['basename'].'DatabaseSeeder';
        $fullPath      = $namespacePath.'\\'.$module['basename'].'\Database\Seeds\\'.$rootSeeder;

        if (class_exists($fullPath)) {
            if ($this->option('class')) {
                $params['--class'] = $this->option('class');
            } else {
                $params['--class'] = $fullPath;
            }

            if ($option = $this->option('database')) {
                $params['--database'] = $option;
            }

            if ($option = $this->option('force')) {
                $params['--force'] = $option;
            }

            $this->call('db:seed', $params);

            event($slug.'.module.seeded', [$module, $this->option()]);
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
            ['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the module\'s root seeder.'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
            ['location', null, InputOption::VALUE_OPTIONAL, 'Which modules location to use.'],
        ];
    }
}
