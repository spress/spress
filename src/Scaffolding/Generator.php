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

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

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

    /**
     * Sets a list of directories in which templates are located.
     *
     * @param array $value
     */
    public function setSkeletonDirs(array $value)
    {
        $this->skeletonDirs = $value;
    }

    /**
     * Render a content using Twig template engine.
     *
     * @param string $template The Twig template
     * @param array  $model    List of key-value properties
     *
     * @return string The redered template
     */
    protected function render($template, $model)
    {
        $twig = $this->getTwig();

        return $twig->render($template, $model);
    }

    /**
     * Return an instance of Twig.
     *
     * @return Environment The Twig instance
     */
    protected function getTwig(): Environment
    {
        $options = [
            'cache' => false,
            'strict_variables' => true,
        ];

        $loader = new FilesystemLoader();
        $loader->setPaths($this->skeletonDirs);

        return new Environment($loader, $options);
    }

    /**
     * Render a template and result is dumped to a file.
     *
     * @param string $template Path to the template file
     * @param string $target   Filename result
     * @param array  $model    key-value array that acts as model
     *
     * @return int|bool Numer of byte that were written or false if error
     */
    protected function renderFile($template, $target, $model)
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        $this->files[] = $target;

        return file_put_contents($target, $this->render($template, $model));
    }

    /**
     * Returns the filenames affected by generator operations.
     *
     * @return array[string]
     */
    protected function getFilesAffected()
    {
        return $this->files;
    }

    /**
     * Cleans the file-affected list.
     */
    protected function cleanFilesAffected()
    {
        $this->files = [];
    }
}
