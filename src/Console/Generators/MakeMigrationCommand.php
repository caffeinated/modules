<?php

namespace Caffeinated\Modules\Console\Generators;

class MakeMigrationCommand extends MakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module:migration
    	{slug : The slug of the module.}
    	{name : The name of the migration.}
    	{--create= : The table to be created.}
        {--table= : The table to migrate.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module migration file';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Migration';

    /**
     * Module folders to be created.
     *
     * @var array
     */
    protected $listFolders = [
        'Database/Migrations/',
    ];

    /**
     * Module files to be created.
     *
     * @var array
     */
    protected $listFiles = [
        '{{filename}}.php',
    ];

    /**
     * Module signature option.
     *
     * @var array
     */
    protected $signOption = [
        'create',
        'table',
    ];

    /**
     * Module stubs used to populate defined files.
     *
     * @var array
     */
    protected $listStubs = [
        'default' => [
            'migration.stub',
        ],

        'create' => [
            'migration_create.stub',
        ],

        'table' => [
            'migration_table.stub',
        ],
    ];

    /**
     * Resolve Container after getting file path.
     *
     * @param string $FilePath
     *
     * @return array
     */
    protected function resolveByPath($filePath)
    {
        $this->container['filename'] = $this->makeFileName($filePath);
        $this->container['classname'] = basename($filePath);
        $this->container['tablename'] = 'dummy';
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
        $this->container['tablename'] = $option;
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
        return date('Y_m_d_His').'_'.strtolower(snake_case(basename($filePath)));
    }

    /**
     * Replace placeholder text with correct values.
     *
     * @return string
     */
    protected function formatContent($content)
    {
        return str_replace(
            [
                '{{filename}}',
                '{{classname}}',
                '{{tablename}}',
            ],
            [
                $this->container['filename'],
                $this->container['classname'],
                $this->container['tablename'],
            ],
            $content
        );
    }
}
