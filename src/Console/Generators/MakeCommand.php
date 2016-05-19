<?php

namespace Caffeinated\Modules\Console\Generators;

use Caffeinated\Modules\Modules;
use Illuminate\Console\Command as CommandGenerator;
use Illuminate\Filesystem\Filesystem;

class MakeCommand extends CommandGenerator
{
    /**
     * Module folders to be created.
     *
     * @var array
     */
    protected $listFolders = [];

    /**
     * Module files to be created.
     *
     * @var array
     */
    protected $listFiles = [];

    /**
     * Module signature option.
     *
     * @var array
     */
    protected $signOption = [];

    /**
     * Module stubs used to populate defined files.
     *
     * @var array
     */
    protected $listStubs = [];

    /**
     * The modules instance.
     *
     * @var Modules
     */
    protected $module;

    /**
     * The modules path.
     *
     * @var string
     */
    protected $modulePath;

    /**
     * The modules info.
     *
     * @var Illuminate\Support\Collection;
     */
    protected $moduleInfo;

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
     * String to store the command type.
     *
     * @var string
     */
    protected $type;

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
        $slug = $this->parseSlug($this->argument('slug'));
        $name = $this->parseName($this->argument('name'));

        if ($this->module->exists($slug)) {
            $this->modulePath = $this->module->getPath();
            $this->moduleInfo = collect($this->module->where('slug', $slug)->first());

            $this->container['slug'] = $slug;
            $this->container['name'] = $name;

            return $this->generate();
        }

        return $this->error('Module '.$this->container['slug'].' does not exist.');
    }

    /**
     * generate the console command.
     *
     * @return mixed
     */
    protected function generate()
    {
        foreach ($this->listFiles as $key => $file) {
            $filePath = $this->makeFilePath($this->listFolders[$key], $this->container['name']);

            $this->resolveByPath($filePath);

            $file = $this->formatContent($file);
            $filePath = str_replace(basename($filePath), '', $filePath);
            $filePath = $filePath.$file;

            if ($this->files->exists($filePath)) {
                return $this->error($this->type.' already exists!');
            }

            $this->makeDirectory($filePath);

            foreach ($this->signOption as $option) {
                if ($this->option($option)) {
                    $stubFile = $this->listStubs[$option][$key];

                    $this->resolveByOption($this->option($option));

                    break;
                }
            }

            if (!isset($stubFile)) {
                $stubFile = $this->listStubs['default'][$key];
            }

            $this->files->put(
                $filePath,
                $this->getStubContent($stubFile)
            );
        }

        return $this->info($this->type.' created successfully.');
    }

    /**
     * Resolve Container after getting file path.
     *
     * @param string $FilePath
     *
     * @return array
     */
    protected function resolveByPath($filePath)
    {
        //
    }

    /**
     * Resolve Container after getting input option.
     *
     * @param string $option
     *
     * @return array
     */
    protected function resolveByOption($option)
    {
        //
    }

    /**
     * Parse slug name of the module.
     *
     * @param string $slug
     *
     * @return string
     */
    protected function parseSlug($slug)
    {
        $slug = studly_case($slug);

        if (str_contains($slug, '/')) {
            $slug = str_replace('/', '', $slug);
        }

        if (str_contains($slug, '\\')) {
            $slug = str_replace('\\', '', $slug);
        }

        return strtolower($slug);
    }

    /**
     * Parse class name of the module.
     *
     * @param string $slug
     *
     * @return string
     */
    protected function parseName($name)
    {
        if (str_contains($name, '\\')) {
            $name = str_replace('\\', '/', $name);
        }

        if (str_contains($name, '/')) {
            $formats = collect(explode('/', $name))->map(function ($name) {
                return studly_case($name);
            });

            $name = $formats->implode('/');
        } else {
            $name = studly_case($name);
        }

        return $name;
    }

    /**
     * Make FilePath.
     *
     * @param string $folder
     * @param string $name
     *
     * @return string
     */
    protected function makeFilePath($folder, $name)
    {
        $folder = ltrim($folder, '\/');
        $folder = rtrim($folder, '\/');

        $name = ltrim($name, '\/');
        $name = rtrim($name, '\/');

        return
            $this->modulePath.DIRECTORY_SEPARATOR.
            $this->moduleInfo->get('namespace').DIRECTORY_SEPARATOR.
            $folder.DIRECTORY_SEPARATOR.$name;
    }

    /**
     * Make FileName.
     *
     * @param string $filePath
     *
     * @return string
     */
    protected function makeFileName($filePath)
    {
        return basename($filePath);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param string $path
     *
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Get Namespace of the current file.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getNamespace($file)
    {
        $namespace = str_replace($this->modulePath, '', $file);
        $namespace = str_replace(basename($namespace), '', $namespace);
        $namespace = ltrim($namespace, '\/');
        $namespace = rtrim($namespace, '\/');

        return str_replace('/', '\\', $namespace);
    }

    /**
     * Get the configured module base namespace.
     *
     * @return string
     */
    protected function getBaseNamespace()
    {
        return $this->module->getNamespace();
    }

    /**
     * Get stub content by key.
     *
     * @param int $key
     *
     * @return string
     */
    protected function getStubContent($stubName)
    {
        $stubPath = __DIR__.'/../../../resources/stubs/';

        return $this->formatContent($this->files->get($stubPath.$stubName));
    }

    /**
     * Replace placeholder text with correct values.
     *
     * @return string
     */
    protected function formatContent($content)
    {
        //
    }
}
