<?php

namespace Caffeinated\Modules\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ModuleUninitializeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:uninitialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninitialize a module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $slug = $this->argument('slug');

        if ($this->laravel['modules']->uninitialize($slug)) {
            $this->info('Module was uninitialized successfully.');
        } else {
            $this->error('Module failed to uninitialize.');
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
