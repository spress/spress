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

use Composer\Config;
use Composer\Config\JsonConfigSource;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Pool;
use Composer\Factory;
use Composer\Installer\InstallationManager;
use Composer\Installer\ProjectInstaller;
use Composer\Json\JsonFile;
use Composer\Package\BasePackage;
use Composer\Package\Version\VersionParser;
use Composer\Package\Version\VersionSelector;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledFilesystemRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Yosymfony\EmbeddedComposer\EmbeddedComposer;
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

    /** @var string */
    const EXTRA_SPRESS_SITE_DIR = 'spress_site_dir';

    /** @var array */
    protected $vcsNames = ['.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg', '.fslckout', '_FOSSIL_'];

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
     * Returns the root directory in which the composer.json is located.
     *
     * @return string
     */
    public function getRootDirectory()
    {
        return $this->embeddedComposer->getExternalRootDirectory();
    }

    /**
     * Installs plugins and themes.
     *
     * @param array    $options      Options for installing packages
     * @param string[] $packageNames List of packages
     *
     * @throws RuntimeException If any problem occurs while resolving dependencies
     */
    public function install(array $options = [], array $packageNames = [])
    {
        $options = $this->getInstallResolver()->resolve($options);
        $installer = $this->buildInstaller($options);
        $installer->setUpdateWhitelist($packageNames);
        $status = $installer->run();

        if ($status !== 0) {
            throw new \RuntimeException(sprintf(
                'An error has been occurred with code: %s while resolving dependencies.',
                $status
            ));
        }
    }

    /**
     * Update plugins and themes installed previously.
     *
     * @param array    $options      Options for updating packages
     * @param string[] $packageNames List of packages
     *
     * @throws RuntimeException If any problem occurs while resolving dependencies
     */
    public function update(array $options = [], array $packageNames = [])
    {
        $options = $this->getInstallResolver()->resolve($options);
        $installer = $this->buildInstaller($options);
        $installer->setUpdate(true);
        $installer->setUpdateWhitelist($packageNames);
        $status = $installer->run();

        if ($status !== 0) {
            throw new \RuntimeException(sprintf(
                'An error has been occurred with code: %s while resolving dependencies.',
                $status
            ));
        }
    }

    /**
     * Adds a new set of packages to the current Composer file.
     *
     * @param array $packageNames
     * @param bool  $areDevPackages
     *
     * @return array List of package's name as a key and the package's version
     *               as value
     */
    public function addPackage(array $packageNames, $areDevPackages = false)
    {
        $fs = new Filesystem();
        $file = $this->embeddedComposer->getExternalComposerFilename();

        if ($fs->exists($file) === false) {
            $fs->dumpFile($file, "{\n}\n");
        }

        if (filesize($file) === 0) {
            $fs->dumpFile($file, "{\n}\n");
        }

        $json = new JsonFile($file);

        $requirements = $this->formatPackageNames($packageNames);
        $versionParser = new VersionParser();

        foreach ($requirements as $constraint) {
            $versionParser->parseConstraints($constraint);
        }

        $composerDefinition = $json->read();

        $requireKey = $areDevPackages ? 'require-dev' : 'require';

        foreach ($requirements as $package => $version) {
            $composerDefinition[$requireKey][$package] = $version;
        }

        $json->write($composerDefinition);

        return $composerDefinition[$requireKey];
    }

    /**
     * Remove a set of packages of the current Composer file.
     *
     * @param array $packageNames
     * @param bool  $areDevPackages
     *
     * @return array List of package's name as a key and the package's version
     *               as value
     */
    public function removePackage(array $packageNames, $areDevPackages = false)
    {
        $packages = $this->formatPackageNames($packageNames);
        $file = $this->embeddedComposer->getExternalComposerFilename();
        $jsonFile = new JsonFile($file);
        $json = new JsonConfigSource($jsonFile);
        $composerDefinition = $jsonFile->read();
        $type = $areDevPackages ? 'require-dev' : 'require';

        foreach ($packages as $name => $version) {
            if (isset($composerDefinition[$type][$name])) {
                $json->removeLink($type, $name);
                unset($composerDefinition[$type][$name]);

                continue;
            }

            $this->io->writeError(sprintf(
                'Package: "%s" is not required and has not been removed.',
                $name
            ));
        }

        return $composerDefinition[$type];
    }

    /**
     * Create a new theme project. This is equivalent to perform a "git clone".
     *
     * @param string $siteDir      Site directory
     * @param string $packageName  Package's name. e.g: "vendory/foo 2.0.0"
     * @param string $repository   Provide a custom repository to search for the package
     * @param bool   $preferSource Install packages from source when available
     *
     * @throws InvalidArgumentException If the package is not a Spress theme or
     *                                  or the host doesn't meet the requirements
     */
    public function createThemeProject($siteDir, $packageName, $repository = null, $preferSource = false)
    {
        $packagePair = new PackageNameVersion($packageName);
        $config = Factory::createConfig();

        if (is_null($repository) === true) {
            $sourceRepo = new CompositeRepository(RepositoryFactory::defaultRepos($this->io, $config));
        } else {
            $sourceRepo = RepositoryFactory::fromString($this->io, $config, $repository, true);
        }

        $pool = new Pool($packagePair->getStability());
        $pool->addRepository($sourceRepo);

        $platformOverrides = $config->get('platform') ?: [];

        $platform = new PlatformRepository([], $platformOverrides);
        $phpPackage = $platform->findPackage('php', '*');
        $phpVersion = $phpPackage->getVersion();
        $prettyPhpVersion = $phpPackage->getPrettyVersion();

        $versionSelector = new VersionSelector($pool);
        $package = $versionSelector->findBestCandidate(
            $packagePair->getName(),
            $packagePair->getVersion(),
            $phpVersion,
            $packagePair->getStability()
        );

        if (is_null($package)) {
            $errorMessage = sprintf(
                'Could not find the theme "%s"',
                $packagePair->getName()
            );

            $versionWithoutPHP = $versionSelector->findBestCandidate(
                $packagePair->getName(),
                $packagePair->getVersion(),
                null,
                $packagePair->getStability()
            );

            if ($phpVersion && $versionWithoutPHP) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '%s in a version installable using your PHP version %s.',
                        $errorMessage,
                        $prettyPhpVersion
                    )
                );
            }

            throw new \InvalidArgumentException($errorMessage.'.');
        }

        if ($package->getType() != self::PACKAGE_TYPE_THEME) {
            throw new \InvalidArgumentException(sprintf(
                'The package "%s" is not a Spress theme.',
                $packageName
            ));
        }

        if (strpos($package->getPrettyVersion(), 'dev-') === 0 && in_array($package->getSourceType(), array('git', 'hg'))) {
            $package->setSourceReference(substr($package->getPrettyVersion(), 4));
        }

        $dm = $this->createDownloadManager($config);
        $dm->setPreferSource($preferSource)
            ->setPreferDist(!$preferSource);

        $projectInstaller = new ProjectInstaller($siteDir, $dm);
        $im = new InstallationManager();
        $im->addInstaller($projectInstaller);
        $im->install(new InstalledFilesystemRepository(new JsonFile('php://memory')), new InstallOperation($package));
        $im->notifyInstalls($this->io);

        if (is_null($repository)) {
            $this->removeVcsMetadata($siteDir);
        }
    }

    /**
     * Rewriting "self.version" dependencies with explicit version numbers
     * of the current composer.json file. Useful if the package's vcs metadata
     * is gone.
     */
    public function rewritingSelfVersionDependencies()
    {
        $composer = $this->embeddedComposer->createComposer($this->io);
        $package = $composer->getPackage();
        $configSource = new JsonConfigSource(new JsonFile('composer.json'));

        foreach (BasePackage::$supportedLinkTypes as $type => $meta) {
            foreach ($package->{'get'.$meta['method']}() as $link) {
                if ($link->getPrettyConstraint() === 'self.version') {
                    $configSource->addLink(
                        $type,
                        $link->getTarget(),
                        $package->getPrettyVersion()
                    );
                }
            }
        }
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
        $packagePair = new PackageNameVersion($packageName);

        if (isset($this->packageCache[$packagePair->getNormalizedNameVersion()])) {
            return $this->packageCache[$packagePair->getNormalizedNameVersion()];
        }

        $composer = $this->embeddedComposer->createComposer($this->io);
        $repoManager = $composer->getRepositoryManager();

        $name = $packagePair->getName();
        $version = $packagePair->getVersion();
        $composerPackage = $this->packageCache[$packageName] = $repoManager->findPackage($name, $version);

        return $composerPackage;
    }

    protected function buildInstaller(array $options)
    {
        $options = $this->getInstallResolver()->resolve($options);

        $composer = $this->embeddedComposer->createComposer($this->io);
        $rootPackage = $composer->getPackage();
        $extras = $rootPackage->getExtra();
        $extras[self::EXTRA_SPRESS_SITE_DIR] = $this->getRootDirectory();

        $rootPackage->setExtra($extras);

        $installer = $this->embeddedComposer->createInstaller($composer, $this->io);
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

    /**
     * Returns a DownloadManager instance.
     *
     * @return Composer\Downloader\DownloadManager
     */
    protected function createDownloadManager(Config $config)
    {
        return (new Factory())->createDownloadManager($this->io, $config);
    }

    /**
     * Formats the packages names.
     *
     * @return array List in which the key is the name and the value is the version
     */
    protected function formatPackageNames(array $packageNames)
    {
        $requires = [];

        foreach ($packageNames as $packageName) {
            $packagePair = new PackageNameVersion($packageName);

            $requires[$packagePair->getName()] = $packagePair->getVersion();
        }

        return $requires;
    }

    /**
     * Clear the VCS metadata. e.g: .git folder. If an error occurred
     * while removing VCS metadata, then its details will be dump using IO.
     *
     * @param string $directory Directory in which find VCS metadata
     */
    protected function removeVcsMetadata($directory)
    {
        $fs = new Filesystem();
        $finder = new Finder();
        $finder
            ->in($directory)
            ->depth(0)
            ->directories()
            ->ignoreVCS(false)
            ->ignoreDotFiles(false);

        foreach ($this->vcsNames as $vcsName) {
            $finder->name($vcsName);
        }

        try {
            foreach ($finder as $dir) {
                $fs->remove($dir);
            }
        } catch (\Exception $e) {
            $this->io->writeError(sprintf(
                'An error occurred while removing the VCS metadata: %s.',
                $e->getMessage()
            ));
        }
    }
}
