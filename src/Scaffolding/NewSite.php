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
use Yosymfony\Spress\PackageManager\PackageManager;
use Yosymfony\Spress\PackageManager\PackageNameVersion;

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

    /** @var PackageManager */
    private $packageManager;

    /**
     * Constructor.
     *
     * @param PackageManager $packageManager Package Manager. If null, only
     *                                       blank themes is allowed
     */
    public function __construct(PackageManager $packageManager = null)
    {
        $this->packageManager = $packageManager;
        $this->fs = new Filesystem();
    }

    /**
     * Create a new site scaffold. In case of exception, the new-site directory
     * will be removed.
     *
     * @param string $path      Destination path
     * @param string $themeName Theme name. Pattern <theme_name>:<theme_version> can be used. "blank" is a special theme
     * @param bool   $force     Force to clear destination if exists and it's not empty'
     *
     * @throws LogicException If there is an attemp of create a non-blank template without the PackageManager
     */
    public function newSite($path, $themeName, $force = false)
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

        try {
            $this->createSite($path, $themeName);
        } catch (\Exception $e) {
            $this->fs->remove($path);

            throw $e;
        }
    }

    private function createSite($path, $themeName)
    {
        if ($themeName !== self::BLANK_THEME && is_null($this->packageManager)) {
            throw new \LogicException(
                'You must set the PackageManager at constructor in order to create non-blank themes.'
            );
        }

        $packageNames = $themeName === self::BLANK_THEME ? [] : [$themeName];
        $this->createBlankSite($path, $packageNames);

        if ($themeName === self::BLANK_THEME) {
            return;
        }

        if ($this->packageManager->existPackage($themeName) === false) {
            throw new \RuntimeException(sprintf('The theme: "%s" does not exist at registered repositories.', $themeName));
        }

        if ($this->packageManager->isThemePackage($themeName) === false) {
            throw new \RuntimeException(sprintf('The theme: "%s" is not a Spress theme.', $themeName));
        }

        $this->packageManager->update();
    }

    private function createBlankSite($path, array $packageNames = [])
    {
        $orgDir = getcwd();

        chdir($path);

        $this->fs->mkdir(['build', 'src/layouts', 'src/content', 'src/content/posts']);
        $this->fs->dumpFile('config.yml', '# Site configuration');
        $this->fs->dumpFile('composer.json', $this->generateContentComposerJsonFile($packageNames));
        $this->fs->dumpFile('src/content/index.html', '');
        $this->fs->mkdir(['src/includes', 'src/plugins']);

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

    /**
     * @param array[string] $packagNames
     *
     * @return string String in JSON format
     */
    private function generateContentComposerJsonFile(array $packagNames = [])
    {
        $jsonPackages = '';

        foreach ($packagNames as $packageName) {
            $nameVersion = new PackageNameVersion($packageName);
            $jsonPackages .= sprintf("\"%s\": \"%s\"\n",
                $nameVersion->getName(),
                $nameVersion->getVersion()
            );
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
