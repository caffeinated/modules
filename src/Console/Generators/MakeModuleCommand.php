<?php

namespace Caffeinated\Modules\Console\Generators;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;

class MakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module
        {slug : The slug of the module}
        {--Q|quick : Skip the make:module wizard and use default values}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Caffeinated module and bootstrap it';

    /**
     * Module folders to be created.
     *
     * @var array
     */
    protected $moduleFolders = [
        'Console/',
        'Database/',
        'Database/Migrations/',
        'Database/Seeds/',
        'Http/',
        'Http/Controllers/',
        'Http/Middleware/',
        'Http/Requests/',
        'Providers/',
        'Resources/',
        'Resources/Lang/',
        'Resources/Views/',
    ];

    /**
     * Module files to be created.
     *
     * @var array
     */
    protected $moduleFiles = [
        'Database/Seeds/{{namespace}}DatabaseSeeder.php',
        'Http/routes.php',
        'Providers/{{namespace}}ServiceProvider.php',
        'Providers/RouteServiceProvider.php',
        'module.json',
    ];

    /**
     * Module stubs used to populate defined files.
     *
     * @var array
     */
    protected $moduleStubs = [
        'seeder.stub',
        'routes.stub',
        'moduleserviceprovider.stub',
        'routeserviceprovider.stub',
        'manifest.stub',
    ];

    /**
     * The modules instance.
     *
     * @var Modules
     */
    protected $module;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Array to store the configuration details.
     *
     * @var array
     */
    protected $container;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Modules    $module
     */
    public function __construct(Filesystem $files, Modules $module)
    {
        parent::__construct();

        $this->files = $files;
        $this->module = $module;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->container['slug'] = strtolower($this->argument('slug'));
        $this->container['name'] = Str::studly($this->container['slug']);
        $this->container['namespace'] = Str::studly($this->container['slug']);
        $this->container['version'] = '1.0';
        $this->container['description'] = 'This is the description for the '.$this->container['name'].' module.';
        $this->container['license'] = 'MIT';
        $this->container['author'] = ' ';

        if ($this->option('quick')) {
            return $this->generate();
        }

        $this->displayHeader('make_module_introduction');

        $this->stepOne();
    }

    /**
     * Step 1: Configure module manifest.
     *
     * @return mixed
     */
    private function stepOne()
    {
        $this->displayHeader('make_module_step_1');

        $this->container['name'] = $this->ask('Please enter the name of the module:', $this->container['name']);
        $this->container['slug'] = $this->ask('Please enter the slug for the module:', $this->container['slug']);
        $this->container['namespace'] = $this->ask('Please enter the namespace for the module:', $this->container['namespace']);
        $this->container['version'] = $this->ask('Please enter the module version:', $this->container['version']);
        $this->container['description'] = $this->ask('Please enter the description of the module:', $this->container['description']);
        $this->container['author'] = $this->ask('Please enter the author of the module:', $this->container['author']);
        $this->container['license'] = $this->ask('Please enter the module license:', $this->container['license']);

        $this->comment('You have provided the following manifest information:');
        $this->comment('Name:        '.$this->container['name']);
        $this->comment('Slug:        '.$this->container['slug']);
        $this->comment('Namespace:   '.$this->container['namespace']);
        $this->comment('Version:     '.$this->container['version']);
        $this->comment('Description: '.$this->container['description']);
        $this->comment('Author:      '.$this->container['author']);
        $this->comment('License:     '.$this->container['license']);

        if ($this->confirm('Do you wish to continue?')) {
            $this->comment('Thanks! That\'s all we need.');
            $this->comment('Now relax while your module is generated for you.');

            $this->generate();
        } else {
            return $this->stepOne();
        }

        return true;
    }

    /**
     * Generate the module.
     */
    protected function generate()
    {
        $steps = [
            'Generating folders...' => 'generateFolders',
            'Generating .gitkeep...' => 'generateGitkeep',
            'Generating files...' => 'generateFiles',
            'Optimizing module cache...' => 'optimizeModules',
        ];

        $progress = new ProgressBar($this->output, count($steps));
        $progress->start();

        foreach ($steps as $message => $function) {
            $progress->setMessage($message);

            $this->$function();

            $progress->advance();
        }

        $progress->finish();

        $this->info("\nModule generated successfully.");
    }

    /**
     * Generate defined module folders.
     */
    protected function generateFolders()
    {
        if (!$this->files->isDirectory($this->module->getPath())) {
            $this->files->makeDirectory($this->module->getPath());
        }

        $this->files->makeDirectory($this->getModulePath($this->container['slug'], true));

        foreach ($this->moduleFolders as $folder) {
            $this->files->makeDirectory($this->getModulePath($this->container['slug']).$folder);
        }
    }

    /**
     * Generate defined module files.
     */
    protected function generateFiles()
    {
        foreach ($this->moduleFiles as $key => $file) {
            $file = $this->formatContent($file);

            $this->files->put($this->getDestinationFile($file), $this->getStubContent($key));
        }
    }

    /**
     * Generate .gitkeep files within generated folders.
     */
    protected function generateGitkeep()
    {
        $modulePath = $this->getModulePath($this->container['slug']);
        foreach ($this->moduleFolders as $folder) {
            $gitkeep = $modulePath.$folder.'/.gitkeep';
            $this->files->put($gitkeep, '');
        }
    }

    /**
     * Reset module cache of enabled and disabled modules.
     */
    protected function optimizeModules()
    {
        return $this->callSilent('module:optimize');
    }

    /**
     * Get the path to the module.
     *
     * @param string $slug
     *
     * @return string
     */
    protected function getModulePath($slug = null, $allowNotExists = false)
    {
        if ($slug) {
            return $this->module->getModulePath($slug, $allowNotExists);
        }

        return $this->module->getPath();
    }

    /**
     * Get destination file.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getDestinationFile($file)
    {
        return $this->getModulePath($this->container['slug']).$this->formatContent($file);
    }

    /**
     * Get stub content by key.
     *
     * @param int $key
     *
     * @return string
     */
    protected function getStubContent($key)
    {
        return $this->formatContent($this->files->get(__DIR__.'/../../../resources/stubs/'.$this->moduleStubs[$key]));
    }

    /**
     * Replace placeholder text with correct values.
     *
     * @return string
     */
    protected function formatContent($content)
    {
        return str_replace(
            ['{{slug}}', '{{name}}', '{{namespace}}', '{{version}}', '{{description}}', '{{author}}', '{{license}}', '{{path}}'],
            [$this->container['slug'], $this->container['name'], $this->container['namespace'], $this->container['version'], $this->container['description'], $this->container['author'], $this->container['license'], $this->module->getNamespace()],
            $content
        );
    }

    /**
     * Pull the given stub file contents and display them on screen.
     *
     * @param string $file
     * @param string $level
     *
     * @return mixed
     */
    protected function displayHeader($file = '', $level = 'info')
    {
        $stub = $this->files->get(__DIR__.'/../../../resources/stubs/console/'.$file.'.stub');

        return $this->$level($stub);
    }
}
