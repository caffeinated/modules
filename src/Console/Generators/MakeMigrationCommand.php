<?php
namespace Caffeinated\Modules\Console\Generators;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeMigrationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:module:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module migration file';

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
        $this->container['slug']          = strtolower($this->argument('slug'));
        $this->container['table']         = strtolower($this->argument('table'));
        $this->container['migrationName'] = snake_case($this->container['table']);
        $this->container['className']     = studly_case($this->container['migrationName']);

        if ($this->module->exists($this->container['slug'])) {
            $this->makeFile();

            $file = pathinfo($this->getDestinationFile(), PATHINFO_FILENAME);

            exec('composer dump-autoload 2>/dev/null');

            return $this->line("<info>Created Module Migration:</info> $file");
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

        return $path.'Database/Migrations/';
    }

    /**
     * Get the migration filename.
     *
     * @return string
     */
    protected function getFilename()
    {
        return date('Y_m_d_His').'_'.$this->container['migrationName'].'.php';
    }

    /**
     * Get the stub content.
     *
     * @return string
     */
    protected function getStubContent()
    {
        return $this->formatContent($this->files->get(__DIR__.'/../../../resources/stubs/migration.stub'));
    }

    /**
	 * Replace placeholder text with correct values.
	 *
	 * @return string
	 */
	protected function formatContent($content)
    {
        return str_replace(
			['{{className}}', '{{table}}'],
			[$this->container['className'], $this->container['table']],
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
            ['table', InputArgument::REQUIRED, 'The name of the database table']
        ];
    }
}
