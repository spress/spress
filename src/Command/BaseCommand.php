<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Command;

use Dflydev\EmbeddedComposer\Core\EmbeddedComposerBuilder;
use Symfony\Component\Console\Command\Command;
use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\PackageManager\ComposerIOBridge;
use Yosymfony\Spress\PackageManager\PackageManager;

/**
 * Base for commands.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class BaseCommand extends Command
{
    /**
     * A shortcut for $this->getApplication->getSpress().
     *
     * @return Spress A Spress instance
     */
    public function getSpress($siteDir = null)
    {
        return $this->getApplication()->getSpress($siteDir);
    }

    /**
     * Returns an instance of PackageManager.
     * It is configured to read a composer.json file.
     *
     * @return PackageManager
     */
    public function getPackageManager($siteDir, IOInterface $io)
    {
        $embeddedComposer = $this->getEmbeddedComposer($siteDir, 'composer.json', 'vendor');
        $embeddedComposer->processAdditionalAutoloads();

        return new PackageManager($embeddedComposer, new ComposerIOBridge($io));
    }

    /**
     * Returns an EmbeddedComposer instance.
     *
     * @return Dflydev\EmbeddedComposer\Core\EmbeddedComposer
     */
    protected function getEmbeddedComposer($siteDir, $composerFilename, $vendorDir)
    {
        $classloader = $this->getApplication()->getClassloader();
        $builder = new EmbeddedComposerBuilder($classloader, $siteDir);

        return $builder
            ->setComposerFilename($composerFilename)
            ->setVendorDirectory($vendorDir)
            ->build();
    }
}
