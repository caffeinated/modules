<?php

namespace Caffeinated\Modules\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ModuleOptimizeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the module cache for better performance';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($location = $this->option('location')) {
            $this->info('Generating optimized module cache...');

            $repository = modules($location);
            $repository->optimize();

            event('modules.optimized', [$repository->all()]);
        } else {
            foreach (modules()->repositories() as $repository) {
                $this->info("Generating optimized module cache for [$repository->location]...");

                $repository->optimize();

                event('modules.optimized', [$repository->all()]);
            }
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['location', null, InputOption::VALUE_OPTIONAL, 'Which modules location to use.'],
        ];
    }
}
