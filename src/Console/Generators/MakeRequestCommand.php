<?php
namespace Caffeinated\Modules\Console\Generators;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeRequestCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:module:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module form request class';

    /**
     * Array to store the configuration details.
     *
     * @var array
     */
    protected $container;

    /**
     * Create a new command instance.
     *
     * @param Filesystem  $files
     * @param Modules  $module
     */
    public function __construct(Filesystem $files, Modules $module)
    {
        parent::__construct();

        $this->files  = $files;
        $this->module = $module;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->container['slug']      = strtolower($this->argument('slug'));
        $this->container['className'] = $this->argument('name');

        if ($this->module->exists($this->container['slug'])) {
            $this->container['module'] = $this->module->getProperties($this->container['slug']);

            $this->makeFile();

            return $this->info('Module request created successfully.');
        }

        return $this->error('Module does not exist.');
    }

    /**
     * Create a new migration file.
     *
     * @return int
     */
    protected function makeFile()
    {
        return $this->files->put($this->getDestinationFile(), $this->getStubContent());
    }

    /**
     * Get file destination.
     *
     * @return string
     */
    protected function getDestinationFile()
    {
        return $this->getPath().$this->formatContent($this->getFilename());
    }

    /**
     * Get module migration path.
     *
     * @return string
     */
    protected function getPath()
    {
        $path = $this->module->getModulePath($this->container['slug']);

        return $path.'Http/Requests/';
    }

    /**
     * Get the migration filename.
     *
     * @return string
     */
    protected function getFilename()
    {
        return $this->container['className'].'.php';
    }

    /**
     * Get the stub content.
     *
     * @return string
     */
    protected function getStubContent()
    {
        return $this->formatContent($this->files->get(__DIR__.'/../../../resources/stubs/request.stub'));
    }

    /**
	 * Replace placeholder text with correct values.
	 *
	 * @return string
	 */
	protected function formatContent($content)
    {
        return str_replace(
			['{{className}}', '{{namespace}}', '{{path}}'],
			[$this->container['className'], $this->container['module']['namespace'], $this->module->getNamespace()],
			$content
		);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['slug', InputArgument::REQUIRED, 'The slug of the module'],
            ['name', InputArgument::REQUIRED, 'The name of the controller']
        ];
    }
}
