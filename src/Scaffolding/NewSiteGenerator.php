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
 * Generates a new site.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewSiteGenerator extends Generator
{
    /** @var string */
    const BLANK_THEME = 'blank';

    /** @var Filesystem */
    private $fs;

    /** @var PackageManager */
    private $packageManager;

    /** @var string */
    private $spressInstallerPackage;

    /**
     * Constructor.
     *
     * @param PackageManager $packageManager Package Manager. If null, only
     *                                       blank themes is allowed
     * @param string The minimum Spress installer package required. e.g: "spress/spress-installer >=2.0"
     */
    public function __construct(PackageManager $packageManager = null, $spressInstallerPackage = 'spress/spress-installer')
    {
        $this->packageManager = $packageManager;
        $this->fs = new Filesystem();
        $this->spressInstallerPackage = $spressInstallerPackage;
    }

    /**
     * Generates a new site scaffold. In case of exception, the new-site directory
     * will be removed.
     *
     * @param string $path      Destination path
     * @param string $themeName Theme name. Pattern <theme_name>:<theme_version> can be used. "blank" is a special theme
     * @param bool   $force     Force to clear destination if exists and it's not empty'
     *
     * @throws LogicException If there is an attemp of create a non-blank template without the PackageManager
     */
    public function generate($path, $themeName, $force = false)
    {
        if (empty(trim($themeName)) === true) {
            throw new \RuntimeException('The name of the theme cannot be empty.');
        }

        $this->fs = new Filesystem();
        $existsPath = $this->fs->exists($path);
        $isEmpty = $this->isEmptyDir($path);

        if ($existsPath === true && $force === false && $isEmpty === false) {
            throw new \RuntimeException(sprintf('Path "%s" exists and is not empty.', $path));
        }

        $existsPath ? $this->clearDir($path) : $this->fs->mkdir($path);

        try {
            $this->createSite($path, $themeName);
        } catch (\Exception $e) {
            $this->fs->remove($path);

            throw $e;
        }
    }

    /**
     * @param string $path
     * @param string $themeName
     */
    private function createSite($path, $themeName)
    {
        if ($themeName !== self::BLANK_THEME && is_null($this->packageManager)) {
            throw new \LogicException(
                'You must set the PackageManager at constructor in order to create non-blank themes.'
            );
        }

        $themePair = new PackageNameVersion($themeName);
        $this->createBlankSite($path, $themePair);

        if ($themeName === self::BLANK_THEME) {
            return;
        }

        if ($this->packageManager->existPackage($themeName) === false) {
            throw new \RuntimeException(sprintf('The theme: "%s" does not exist at registered repositories.', $themeName));
        }

        if ($this->packageManager->isThemePackage($themeName) === false) {
            throw new \RuntimeException(sprintf('The theme: "%s" is not a Spress theme.', $themeName));
        }

        if ($this->packageManager->isPackageDependOn($themeName, $this->spressInstallerPackage) === false) {
            throw new \RuntimeException(sprintf(
                sprintf('This version of Spress requires a theme with "%s".', $this->spressInstallerPackage),
                $themeName
            ));
        }

        $this->packageManager->update();
        $this->copyContentFromThemeToSite($path, $themePair);
    }

    /**
     * @param string             $path
     * @param PackageNameVersion $themePair
     */
    private function createBlankSite($path, PackageNameVersion $themePair)
    {
        $packagePairs = [];
        $defaultTheme = '';

        if ($themePair->getName() !== self::BLANK_THEME) {
            $defaultTheme = $themePair->getName();
            array_push($packagePairs, $themePair);
        }

        $orgDir = getcwd();

        chdir($path);

        $this->fs->mkdir(['build', 'src/layouts', 'src/content', 'src/content/posts']);
        $this->renderFile('site/config.yml.twig', 'config.yml', [
            'default_theme' => $defaultTheme,
        ]);
        $this->renderFile('site/composer.json.twig', 'composer.json', [
            'requires' => $this->generateRequirePackages($packagePairs),
        ]);
        $this->fs->dumpFile('src/content/index.html', '');
        $this->fs->mkdir(['src/includes', 'src/plugins']);

        chdir($orgDir);
    }

    /**
     * @param string             $path
     * @param PackageNameVersion $themePair
     */
    private function copyContentFromThemeToSite($path, PackageNameVersion $themePair)
    {
        $relativeThemePath = 'src/themes/'.$themePair->getName();

        if ($this->fs->exists($path.'/'.$relativeThemePath) === false) {
            throw new \RuntimeException('The theme has not been installed correctly.');
        }

        $finder = new Finder();
        $finder->in($path.'/'.$relativeThemePath.'/src/content')
            ->exclude(['assets'])
            ->files();

        foreach ($finder as $file) {
            $this->fs->copy($file->getRealpath(), $path.'/src/content/'.$file->getRelativePathname());
        }
    }

    /**
     * @param string $path
     *
     * @return bool
     */
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

    /**
     * @param string $path
     */
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
     * @param PackageNameVersion[] $packagePairs
     *
     * @return array List of packages in which the key is the package's name
     *               and the value is the package's version
     */
    private function generateRequirePackages(array $packagePairs)
    {
        $requires = [];

        foreach ($packagePairs as $packagePair) {
            $requires[$packagePair->getName()] = $packagePair->getVersion();
        }

        return $requires;
    }
}
