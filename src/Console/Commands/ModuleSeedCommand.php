<?php

namespace Caffeinated\Modules\Console\Commands;

use Caffeinated\Modules\ModuleRepositoriesManager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
     * @var ModuleRepositoriesManager
     */
    protected $module;

    /**
     * Create a new command instance.
     *
     * @param ModuleRepositoriesManager $module
     */
    public function __construct(ModuleRepositoriesManager $module)
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
        $slug = $this->argument('slug');

        if (isset($slug)) {
            if (!modules($this->option('location'))->exists($slug)) {
                return $this->error('Module does not exist.');
            }

            if (modules($this->option('location'))->isEnabled($slug)) {
                $this->seed($slug);
            } elseif ($this->option('force')) {
                $this->seed($slug);
            }

            return;
        } else {
            if ($this->option('force')) {
                $modules = modules($this->option('location'))->all();
            } else {
                $modules = modules($this->option('location'))->enabled();
            }

            foreach ($modules as $module) {
                $this->seed($module['slug']);
            }
        }
    }

    /**
     * Seed the specific module.
     *
     * @param string $module
     *
     * @return array
     */
    protected function seed($slug)
    {
        $module = modules($this->option('location'))->where('slug', $slug);
        $params = [];
        $namespacePath = modules($this->option('location'))->getNamespace();
        $rootSeeder = $module['basename'].'DatabaseSeeder';
        $fullPath = $namespacePath.'\\'.$module['basename'].'\Database\Seeds\\'.$rootSeeder;

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
