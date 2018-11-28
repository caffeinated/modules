<?php

namespace Caffeinated\Modules\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleEnableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:enable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable a module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $slug = $this->argument('slug');

        if (modules($this->option('location'))->isDisabled($slug)) {
            modules($this->option('location'))->enable($slug);

            $module = modules($this->option('location'))->where('slug', $slug);

            event($slug.'.module.enabled', [$module, null]);

            $this->info('Module was enabled successfully.');
        } else {
            $this->comment('Module is already enabled.');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['slug', InputArgument::REQUIRED, 'Module slug.'],
        ];
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
