<?php

/*
 *
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace App\Services;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Class General
 *
 * @package App\Libraries
 */
class Twig
{
    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var string
     */
    private $ext = '.twig';

    /**
     * Twig constructor.
     *
     * @param $templateFolder:主题的文件夹名
     */
    public function __construct($templateFolder)
    {
        $loader = new FilesystemLoader(app()->basePath() . '/public/theme' . DIRECTORY_SEPARATOR . $templateFolder);

        if (!is_writable(app()->storagePath() . '/framework/cache') || env("APP_ENV") == "development") {
            $dataConfig['cache'] = app()->storagePath() . '/framework/cache';
            $dataConfig['auto_reload'] = true;
        }

        if (env("APP_ENV") == "development") {
            $dataConfig['debug'] = true;
        }

        $dataConfig['autoescape'] = false;

        $this->environment = new Environment($loader, $dataConfig);
        //$this->environment->addExtension(new CoreExtension());
        if (env("APP_ENV") == "development") {
            $this->environment->addExtension(new DebugExtension());
        }
    }

    /**
     * @param string $file
     * @param array $array
     *
     * @return string
     */
    public function render(string $file, array $array): string
    {

        $template = $this->environment->load($file . $this->ext);

        return $template->render($array);
    }
}
