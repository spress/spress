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
use Symfony\Component\Yaml\Yaml;
use Yosymfony\Spress\PackageManager\PackageManager;
use Yosymfony\Spress\PackageManager\PackageNameVersion;

/**
 * Generates a new site.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SiteGenerator extends Generator
{
    /** @var string */
    const BLANK_THEME = 'blank';

    /** @var Filesystem */
    protected $fs;

    /** @var PackageManager */
    protected $packageManager;

    /** @var string */
    protected $spressInstallerPackage;

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
     * Scaffold a new site. In case of exception, the new-site directory
     * will be removed.
     *
     * @param string $path         Destination path
     * @param string $themeName    Theme name. Pattern <theme_name>:<theme_version> can be used. "blank" is a special theme
     * @param bool   $force        Force to clear destination if exists and it's not empty'
     * @param bool   $preferSource
     * @param bool   $preferLock
     * @param bool   $noScripts
     *
     * @throws LogicException If there is an attemp of create a non-blank template without the PackageManager
     */
    public function generate($path, $themeName, $force = false, $preferSource = false, $preferLock = false, $noScripts = false)
    {
        $this->checkThemeName($themeName);
        $this->processPath($path, $force);

        try {
            $this->createSite($path, $themeName, $preferLock, $preferSource, $noScripts);
        } catch (\Exception $e) {
            $this->fs->remove($path);

            throw $e;
        }
    }

    /**
     * Create a site.
     *
     * @param string $path
     * @param string $themeName
     *
     * @throws LogicException           If the packageManager is null
     * @throws RuntimeException         If an error occurs while installing the theme
     * @throws InvalidArgumentException If the theme's name is invalid
     */
    protected function createSite($path, $themeName, $preferLock, $preferSource, $noScripts)
    {
        $this->checkRequirements($themeName);

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

        $this->updateDependencies($preferLock, $preferSource, $noScripts);

        $relativeThemePath = 'src/themes/'.$themePair->getName();
        $this->copyContentFromThemeToSite($path, $relativeThemePath);
        $this->setUpSiteConfigFile($path, $relativeThemePath, $themePair->getName());
    }

    /**
     * Updates the site dependencies.
     *
     * @param bool $preferLock
     * @param bool $preferSource
     * @param bool $noScripts
     */
    protected function updateDependencies($preferLock, $preferSource, $noScripts)
    {
        $pmOptions = [
            'prefer-source' => $preferSource,
            'prefer-dist' => !$preferSource,
            'no-scripts' => $noScripts,
        ];

        if ($preferLock === true) {
            $this->packageManager->install($pmOptions);
        } else {
            $this->packageManager->update($pmOptions);
        }
    }

    /**
     * Checks the theme's name.
     *
     * @param string $themeName
     */
    protected function checkThemeName($themeName)
    {
        if (empty(trim($themeName)) === true) {
            throw new \InvalidArgumentException('The name of the theme cannot be empty.');
        }
    }

    /**
     * Checks the requirements for the theme.
     *
     * @throws LogicException If the packageManager is null
     */
    protected function checkRequirements($themeName)
    {
        if ($themeName !== self::BLANK_THEME && is_null($this->packageManager)) {
            throw new \LogicException(
                'You must set the PackageManager at the constructor in order to create non-blank themes.'
            );
        }
    }

    /**
     * Process the path of the future site.
     *
     * @param string $path
     * @param bool   $force
     *
     * @throws RuntimeException If the argument force is set to true and the
     *                          path exists and is not empty
     */
    protected function processPath($path, $force)
    {
        $existsPath = $this->fs->exists($path);
        $isEmpty = $this->isEmptyDir($path);

        if ($existsPath === true && $force === false && $isEmpty === false) {
            throw new \RuntimeException(sprintf('Path "%s" exists and is not empty.', $path));
        }

        $existsPath ? $this->clearDir($path) : $this->fs->mkdir($path);
    }

    /**
     * @param string             $path
     * @param PackageNameVersion $themePair
     */
    protected function createBlankSite($path, PackageNameVersion $themePair)
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
     * Copies content files from the theme to "src/content" folder.
     *
     * @param string $sitePath
     * @param string $relativeThemePath
     */
    protected function copyContentFromThemeToSite($sitePath, $relativeThemePath)
    {
        if ($this->fs->exists($sitePath.'/'.$relativeThemePath) === false) {
            throw new \RuntimeException('The theme has not been installed correctly.');
        }

        $finder = new Finder();
        $finder->in($sitePath.'/'.$relativeThemePath.'/src/content')
            ->exclude(['assets'])
            ->files();

        foreach ($finder as $file) {
            $this->fs->copy($file->getRealpath(), $sitePath.'/src/content/'.$file->getRelativePathname(), true);
        }
    }

    /**
     * Sets up the configuration file.
     *
     * @param string $sitePath
     * @param string $relativeThemePath
     * @param string $themeName
     */
    protected function setUpSiteConfigFile($sitePath, $relativeThemePath, $themeName)
    {
        $source = $sitePath.'/'.$relativeThemePath.'/config.yml';
        $destination = $sitePath.'/config.yml';

        $this->fs->copy($source, $destination, true);

        $configContent = file_get_contents($destination);
        $configValues = Yaml::parse($configContent);
        $configValues['themes'] = ['default' => $themeName];

        $configParsed = Yaml::dump($configValues);

        $this->fs->dumpFile($destination, $configParsed);
    }

    /**
     * Is the path empty?
     *
     * @param string $path
     *
     * @return bool
     */
    protected function isEmptyDir($path)
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
     * Clears the directory.
     *
     * @param string $path
     */
    protected function clearDir($path)
    {
        $items = [];
        $finder = new Finder();
        $finder->in($path)
            ->ignoreVCS(false)
            ->ignoreDotFiles(false);

        foreach ($finder as $item) {
            $items[] = $item->getRealpath();
        }

        if (count($items) > 0) {
            $this->fs->remove($items);
        }
    }

    /**
     * Generates the list of required packages.
     *
     * @param PackageNameVersion[] $packagePairs
     *
     * @return array List of packages in which the key is the package's name
     *               and the value is the package's version
     */
    protected function generateRequirePackages(array $packagePairs)
    {
        $requires = [];

        foreach ($packagePairs as $packagePair) {
            $requires[$packagePair->getName()] = $packagePair->getVersion();
        }

        return $requires;
    }
}
