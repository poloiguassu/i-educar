<?php

class TemplateRenderer
{
    public $loader;
    public $environment;

    public function __construct($envOptions = [], $templateDirs = [])
    {

        $cachePath = false;

        $relativePath = $GLOBALS['coreExt']['Config']->app->template->twig_cache;

        if ($relativePath) {
            $cacheTwigPath = PROJECT_ROOT . $relativePath;

            if (is_dir($cacheTwigPath) && is_writable($cacheTwigPath)) {
                $cachePath = $cacheTwigPath;
            }
        }

        $envOptions += [
            'debug' => false,
            'charset' => 'UTF-8',
            'cache' => $cachePath,
            'strict_variables' => true,
        ];

        $rootDir = realpath(__DIR__ . '/../');

        $rootPath ='../../resources/views';

        $templateDirs = array_merge(
            [$rootPath], // Base directory with all templates
            $templateDirs
        );

        $this->loader = new Twig_Loader_Filesystem($templateDirs);

        $this->loader->addPath($rootPath . '/pages', 'pages');
        $this->loader->addPath($rootPath . '/partials', 'partials');

        $this->environment = new Twig_Environment($this->loader, $envOptions);
    }

    public function render($templateFile, array $variables)
    {
        return $this->environment->render($templateFile . '.html.twig', $variables);
    }
}
