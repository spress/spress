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

use Composer\Package\Version\VersionParser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Creates a new site.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewSite
{
    /** @var string */
    const BLANK_THEME = 'blank';

    /** @var Filesystem */
    private $fs;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    /**
     * Create a new site scaffold.
     *
     * @param string $path             Destination path
     * @param string $themeName        Theme name. Pattern <theme_name>:<theme_version> can be used. "blank" is a special theme
     * @param bool   $force            Force to clear destination if exists and it's not empty'
     * @param bool   $completeScaffold If true adds "includes" and "plugins" folders to the site
     */
    public function newSite($path, $themeName, $force = false, $completeScaffold = false)
    {
        $this->fs = new Filesystem();
        $existsPath = $this->fs->exists($path);
        $isEmpty = $this->isEmptyDir($path);

        if ($existsPath === true && $force === false && $isEmpty === false) {
            throw new \RuntimeException(sprintf('Path "%s" exists and is not empty.', $path));
        }

        if ($existsPath) {
            $this->clearDir($path);
        } else {
            $this->fs->mkdir($path);
        }

        $this->createSite($path, $themeName, $completeScaffold);
    }

    private function createSite($path, $themeName, $completeScaffold = false)
    {
        $packageNames = $themeName === self::BLANK_THEME ? [] : [$themeName];
        $this->createBlankSite($path, $completeScaffold, $packageNames);
    }

    private function createBlankSite($path, $completeScaffold, array $packageNames = [])
    {
        $orgDir = getcwd();

        chdir($path);

        $this->fs->mkdir(['build', 'src/layouts', 'src/content', 'src/content/posts']);
        $this->fs->dumpFile('config.yml', '# Site configuration');
        $this->fs->dumpFile('composer.json', $this->generateContentComposerJsonFile($packageNames));
        $this->fs->dumpFile('src/content/index.html', '');

        if ($completeScaffold === true) {
            $this->fs->mkdir(['src/includes', 'src/plugins']);
        }

        chdir($orgDir);
    }

    private function isEmptyDir($path)
    {
        if ($this->fs->exists($path) === true) {
            $finder = new Finder();
            $finder->in($path);

            $iterator = $finder->getIterator();
            $iterator->next();

            return iterator_count($iterator) === 0;
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

    private function generateContentComposerJsonFile(array $packagNames = [])
    {
        $versionParser = new VersionParser();
        $requires = $versionParser->parseNameVersionPairs($packagNames);
        $jsonPackages = '';

        foreach ($requires as $pair) {
            $version = isset($pair['version']) ? $pair['version'] : '*';
            $jsonPackages .= sprintf("\"%s\": \"%s\"\n", $pair['name'], $version);
        }

        $result = <<<"eot"
{
    "require": {
            $jsonPackages
    }
}
eot;

        return $result;
    }
}
