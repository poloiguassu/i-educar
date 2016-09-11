<?php

// Twig's autoloader will take care of loading required classes
require_once 'lib/vendor/autoload.php';
Twig_Autoloader::register();

class TemplateRenderer
{
	public $loader;
	public $environment;

	public function __construct($envOptions = array(), $templateDirs = array())
	{
		// Merge default options
		// You may want to change these settings

		$envOptions += array(
			'debug' => false,
			'charset' => 'iso-8859-1',
			'cache' => PROJECT_ROOT . '/tmp/twig/cache', // Store cached files under cache directory
			'strict_variables' => true,
		);
		
		$templateDirs = array_merge(
			array(PROJECT_ROOT . '/views'), // Base directory with all templates
			$templateDirs
		);
		
		$this->loader = new Twig_Loader_Filesystem($templateDirs);
		$this->environment = new Twig_Environment($this->loader, $envOptions);
	}

	public function render($templateFile, array $variables)
	{
		return $this->environment->render($templateFile, $variables);
	}
}
