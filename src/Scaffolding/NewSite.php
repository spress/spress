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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Creates a new site.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewSite
{
    private $templatePath;
    private $fs;

    public function __construct($templatePath)
    {
        $this->templatePath = $templatePath;
        $this->fs = new Filesystem();
    }

    /**
     * Create a new site scaffold.
     *
     * @param string $path         Destination path
     * @param string $templateName Template name. "blank" is a special template.
     * @param bool   $force        Force to clear destination if exists and it's not empty'
     */
    public function newSite($path, $templateName, $force = false, $completeScaffold = false)
    {
        $this->fs = new Filesystem();
        $existsPath = $this->fs->exists($path);
        $isEmpty = $this->isEmptyDir($path);

        if ($existsPath && false === $force && false === $isEmpty) {
            throw new \RuntimeException(sprintf('Path "%s" exists and is not empty.', $path));
        }

        if ($existsPath) {
            $this->clearDir($path);
        } else {
            $this->fs->mkdir($path);
        }

        $this->createSite($path, $templateName, $completeScaffold);
    }

    private function createSite($path, $templateName, $completeScaffold = false)
    {
        if ('blank' == $templateName) {
            $this->createBlankSite($path, $completeScaffold);
        } else {
            $this->copyTemplate($path, $templateName);
        }
    }

    private function createBlankSite($path, $completeScaffold)
    {
        $orgDir = getcwd();

        chdir($path);

        $this->fs->mkdir(['build', 'src/layouts', 'src/content', 'src/content/posts']);
        $this->fs->dumpFile('config.yml', '# Site configuration');
        $this->fs->dumpFile('composer.json', $this->getContentComposerJsonFile());
        $this->fs->dumpFile('src/content/index.html', '');

        if (true === $completeScaffold) {
            $this->fs->mkdir(['src/includes', 'src/plugins']);
        }

        chdir($orgDir);
    }

    private function copyTemplate($path, $templateName)
    {
        $templatePath = $this->getTemplatePath($templateName);

        if (false === $this->fs->exists($templatePath)) {
            throw new \InvalidArgumentException(sprintf('The template "%s" not exists.', $templateName));
        }

        $this->fs->mirror($templatePath, $path);
    }

    private function isEmptyDir($path)
    {
        if ($this->fs->exists($path)) {
            $finder = new Finder();
            $finder->in($path);

            $iterator = $finder->getIterator();
            $iterator->next();

            return !$iterator->valid();
        }

        return true;
    }

    private function clearDir($path)
    {
        $items = [];
        $finder = new Finder();
        $finder->in($path);

        foreach ($finder as $item) {
            $items[] = $item->getRealpath();
        }

        if (count($items) > 0) {
            $this->fs->remove($items);
        }
    }

    private function getTemplatePath($templateName)
    {
        return $this->templatePath.'/'.$templateName;
    }

    private function getContentComposerJsonFile()
    {
        $result = <<<eot
{
    "name": "vendor/your-theme-name",
    "description": "The description for your theme",
    "license": "MIT",
    "type": "spress-theme",
    "require": {
            "yosymfony/spress-installer": "2.0.*"
    }
}
eot;

        return $result;
    }
}
