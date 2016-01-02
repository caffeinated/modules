<?php
namespace Caffeinated\Modules\Console\Commands;

use Illuminate\Console\Command;

class ModuleCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
	protected $name = 'module:cache';

    /**
     * The console command description.
     *
     * @var string
     */
	protected $description = 'Reset cached instance of enabled and disabled modules';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->laravel['modules']->cache();

		$this->info("Modules were successfully cached.");
	}
}
