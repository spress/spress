<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\PackageManager;

use Dflydev\EmbeddedComposer\Core\EmbeddedComposer;
use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\Core\Support\AttributesResolver;

/**
 * A simple Composer based package manager.
 * If you experiencing issues with memory and update operation see.
 *
 * @link https://github.com/composer/composer/issues/1898#issuecomment-26684281
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PackageManager
{
    /** @var string */
    const PACKAGE_TYPE_THEME = 'spress-theme';

    /** @var array List of packages with the package's name as key */
    protected $packageCache = [];

    /** @var string */
    protected $siteRoot;

    /** @var EmbeddedComposer */
    protected $embeddedComposer;

    /** @var Composer\IO\IOInterface */
    protected $io;

    /**
     * Constructor.
     *
     * @param EmbeddedComposer $embeddedComposer
     * @param IOInterface      $io
     */
    public function __construct(EmbeddedComposer $embeddedComposer, IOInterface $io)
    {
        $this->siteRoot = $embeddedComposer->getExternalRootDirectory();
        $this->embeddedComposer = $embeddedComposer;
        $this->io = new ComposerIOBridge($io);
    }

    /**
     * Installs plugins and themes.
     *
     * @param array    $options      Options for installing packages
     * @param string[] $packageNames List of packages
     */
    public function install(array $options = [], array $packageNames = [])
    {
        $options = $this->getInstallResolver()->resolve($options);
        $installer = $this->buildInstaller($options);
        $installer->run();
    }

    /**
     * Update plugins and themes installed previously.
     *
     * @param array    $options      Options for updating packages
     * @param string[] $packageNames Lista de packages
     */
    public function update(array $options = [], array $packageNames = [])
    {
        $options = $this->getInstallResolver()->resolve($options);
        $installer = $this->buildInstaller($options);
        $installer->setUpdate(true);
        $installer->setUpdateWhitelist($packageNames);
        $installer->run();
    }

    /**
     * Clear all package in cache.
     */
    public function clearPackageCache()
    {
        $this->packageCache = [];
    }

    /**
     * Determines if a package exists in the registered repositories
     * (packagist.org for example).
     *
     * @param string $packageName Package's name. It could be a pair of
     *                            name and version as the follow: "spress/foo:v1.0.0". If no version
     *                            supplied the latest version will be used
     *
     * @return bool
     */
    public function existPackage($packageName)
    {
        return !is_null($this->findPackageGlobal($packageName));
    }

    /**
     * Determines if package is a Spress theme.
     *
     * @return bool
     *
     * @throws RuntimeException If the package doesn't exist
     */
    public function isThemePackage($packageName)
    {
        $composerPackage = $this->findPackageGlobal($packageName);

        if (is_null($composerPackage) === true) {
            throw new \RuntimeException(sprintf('The theme: "%s" does not exist.', $packageName));
        }

        return $composerPackage->getType() == self::PACKAGE_TYPE_THEME;
    }

    /**
     * Determines if a package A depends on package B. Only scan with first deep level.
     *
     * @param string $packageNameA Packagae A. e.g: "vendor/foo *"
     * @param string $packageNameB Package B. e.g: "vendor/foo-b" or "vendor/foo-b >=2.0"
     *
     * @return bool
     *
     * @throws RuntimeException If the package doesn't exist
     */
    public function isPackageDependOn($packageNameA, $packageNameB)
    {
        $composerPackageA = $this->findPackageGlobal($packageNameA);

        if (is_null($composerPackageA) === true) {
            throw new \RuntimeException(sprintf('The package: "%s" does not exist.', $packageNameA));
        }

        $requires = $composerPackageA->getRequires();
        $pairPackageB = new PackageNameVersion($packageNameB);

        foreach ($requires as $link) {
            if ($link->getTarget() == $pairPackageB->getName()) {
                if ($link->getConstraint()->matches($pairPackageB->getComposerVersionConstraint())) {
                    return true;
                }

                return false;
            }
        }

        return false;
    }

    /**
     * Recovers the data of a package registered in repositories such as packagist.org.
     *
     * @param string $packageName Package's name
     *
     * @return Composer\Package\PackageInterface|null Null if the package not found
     */
    protected function findPackageGlobal($packageName)
    {
        $packageVersion = new PackageNameVersion($packageName);

        if (isset($this->packageCache[$packageVersion->getNormalizedNameVersion()])) {
            return $this->packageCache[$packageVersion->getNormalizedNameVersion()];
        }

        $composer = $this->embeddedComposer->createComposer($this->io);
        $repoManager = $composer->getRepositoryManager();

        $name = $packageVersion->getName();
        $version = $packageVersion->getVersion();
        $composerPackage = $this->packageCache[$packageName] = $repoManager->findPackage($name, $version);

        return $composerPackage;
    }

    protected function buildInstaller(array $options)
    {
        $options = $this->getInstallResolver()->resolve($options);
        $installer = $this->embeddedComposer->createInstaller($this->io);
        $installer
            ->setDryRun($options['dry-run'])
            ->setVerbose($options['verbose'])
            ->setPreferSource($options['prefer-source'])
            ->setPreferDist($options['prefer-dist'])
            ->setDevMode(!$options['no-dev'])
            ->setDumpAutoloader(!$options['no-autoloader'])
            ->setRunScripts(!$options['no-scripts'])
            ->setSkipSuggest($options['no-suggest'])
            ->setOptimizeAutoloader($options['optimize-autoloader'])
            ->setClassMapAuthoritative($options['classmap-authoritative'])
            ->setIgnorePlatformRequirements($options['ignore-platform-reqs']);

        return $installer;
    }

    protected function getInstallResolver()
    {
        $resolver = new AttributesResolver();
        $resolver->setDefault('dry-run', false, 'bool')
            ->setDefault('verbose', false, 'bool')
            ->setDefault('prefer-dist', true, 'bool')
            ->setDefault('prefer-source', false, 'bool')
            ->setDefault('no-dev', true, 'bool')
            ->setDefault('no-autoloader', false, 'bool')
            ->setDefault('no-scripts', false, 'bool')
            ->setDefault('no-suggest', true, 'bool')
            ->setDefault('optimize-autoloader', false, 'bool')
            ->setDefault('classmap-authoritative', false, 'bool')
            ->setDefault('ignore-platform-reqs', false, 'bool');

        return $resolver;
    }
}
