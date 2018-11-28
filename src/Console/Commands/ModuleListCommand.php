<?php

namespace Caffeinated\Modules\Console\Commands;

use Illuminate\Console\Command;
use Caffeinated\Modules\Repositories\Repository;

class ModuleListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'module:list {--location=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all application modules';

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['#', 'Location', 'Name', 'Slug', 'Description', 'Status'];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($location = $this->option('location')) {
            $repository = modules($location);

            if ($repository->count() == 0) {
                $this->error("Your application doesn't have any modules.");
            }

            $this->displayModules($this->getModules($repository));
        } else {
            foreach (modules()->repositories() as $repository) {
                $modules = $repository->all();

                if (count($modules) == 0) {
                    $this->error("Your application doesn't have any modules.");
                }

                $this->displayModules($this->getModules($repository));
            }
        }
    }

    /**
     * Get all modules.
     *
     * @param $repository
     * @return array
     */
    protected function getModules(Repository $repository)
    {
        $modules = $repository->all();
        $results = [];

        foreach ($modules as $module) {
            $results[] = $this->getModuleInformation($repository, $module);
        }

        return array_filter($results);
    }

    /**
     * Returns module manifest information.
     *
     * @param Repository $repository
     * @param array $module
     * @return array
     */
    protected function getModuleInformation(Repository $repository, $module)
    {
        return [
            '#'           => $module['order'],
            'location'    => $repository->location,
            'name'        => isset($module['name']) ? $module['name'] : '',
            'slug'        => $module['slug'],
            'description' => isset($module['description']) ? $module['description'] : '',
            'status'      => (modules($repository->location)->isEnabled($module['slug'])) ? 'Enabled' : 'Disabled',
        ];
    }

    /**
     * Display the module information on the console.
     *
     * @param array $modules
     */
    protected function displayModules(array $modules)
    {
        $this->table($this->headers, $modules);
    }
}
