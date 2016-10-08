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
    private $siteRoot;

    /** @var EmbeddedComposer */
    private $embeddedComposer;

    /** @var Composer\IO\IOInterface */
    private $io;

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

    private function buildInstaller(array $options)
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

    private function getInstallResolver()
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
