<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Scaffolding;

/**
 * Base class for generators.
 * Inspired by {@link https://github.com/sensiolabs/SensioGeneratorBundle/blob/master/Generator/Generator.php Symfony Generator}.
 *
 * @author Victor Puertas <vpuertas@gmail.com>
 */
class Generator
{
    private $files = [];
    private $skeletonDirs;

    public function __construct()
    {
        \Twig_Autoloader::register();
    }

    /**
     * Set a list of directories.
     *
     * @param array $value
     */
    public function setSkeletonDirs(array $value)
    {
        $this->skeletonDirs = $value;
    }

    protected function render($template, $model)
    {
        $twig = $this->getTwig();

        return $twig->render($template, $model);
    }

    protected function getTwig()
    {
        $options = [
            'cache' => false,
            'strict_variables' => true,
        ];

        $loader = new \Twig_Loader_Filesystem();
        $loader->setPaths($this->skeletonDirs);

        return new \Twig_Environment($loader, $options);
    }

    protected function renderFile($template, $target, $model)
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        $this->files[] = $target;

        return file_put_contents($target, $this->render($template, $model));
    }

    protected function getFilesAffected()
    {
        return $this->files;
    }

    protected function cleanFilesAffected()
    {
        $this->files = [];
    }
}
