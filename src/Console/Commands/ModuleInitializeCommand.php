<?php

namespace Caffeinated\Modules\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ModuleInitializeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize a module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $slug = $this->argument('slug');

        if ($this->laravel['modules']->initialize($slug)) {
            $this->info('Module was initialized successfully.');
        } else {
            $this->error('Module failed to initialize.');
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
}
