<?php
namespace Caffeinated\Modules;

use Countable;
use Illuminate\View\Factory;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Config\Repository;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\Translator;
use Illuminate\Database\Eloquent\Collection;

class FileMissingException extends \Exception {}

class Modules implements Countable
{
	/**
	 * @var Finder
	 */
	protected $finder;

	/**
	 * @var Repository
	 */
	protected $config;

	/**
	 * @var Translator
	 */
	protected $lang;

	/**
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * @var Factory
	 */
	protected $views;

	/**
	 * @var HtmlBuilder
	 */
	protected $html;

	/**
	 * @var UrlGenerator
	 */
	protected $url;

	/**
	 * Constructor method
	 *
	 * @param Finder $finder
	 * @param Repository $config
	 * @param Factory $view
	 * @param Translator $lang
	 */
	public function __construct(
		Finder $finder,
		Repository $config,
		Factory $views,
		Translator $lang,
		Filesystem $files,
		HtmlBuilder $html,
		UrlGenerator $url
	) {
		$this->finder = $finder;
		$this->config = $config;
		$this->lang   = $lang;
		$this->views  = $views;
		$this->files  = $files;
		$this->html   = $html;
		$this->url    = $url;
	}
}