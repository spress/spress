<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\Plugin;

use Yosymfony\EmbeddedComposer\EmbeddedComposerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Yosymfony\Spress\Core\Plugin\PluginManagerBuilder;
use Yosymfony\Spress\Core\Plugin\PluginManager;

class PluginManagerBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected $pluginDir;
    protected $embeddedComposer;

    public function setUp()
    {
        $this->pluginDir = __DIR__.'/../fixtures/project/src/plugins';
        $vendorDir = __DIR__.'/../fixtures/project/vendor';

        $autoloaders = spl_autoload_functions();

        $embeddedComposerBuilder = new EmbeddedComposerBuilder($autoloaders[0][0]);
        $this->embeddedComposer = $embeddedComposerBuilder
            ->setComposerFilename('composer.json')
            ->setVendorDirectory($vendorDir)
            ->build();
        $this->embeddedComposer->processAdditionalAutoloads();
    }

    public function testBuildMustReturnsAPluginManagerInstance()
    {
        $builder = new PluginManagerBuilder($this->pluginDir, new EventDispatcher());

        $this->assertInstanceOf(PluginManager::class, $builder->build(), 'Failed to retrieve the PluginManager instance');
    }

    public function testGetPluginCollectionMustReturnsThePluginCollectionFilledWithTheValidPluginsFoundInThePluginFolder()
    {
        $builder = new PluginManagerBuilder($this->pluginDir, new EventDispatcher());
        $pm = $builder->build();
        $pluginCollection = $pm->getPluginCollection();

        $this->assertCount(2, $pluginCollection, 'The number of plugins in the collection is wrong');

        $plugin = $pluginCollection->get('Test plugin');
        $metas = $plugin->getMetas();

        $this->assertEquals('Test plugin', $metas['name'], 'Failed to retrieve the name of the plugin from metas');

        $plugin = $pluginCollection->get('Hello plugin');
        $metas = $plugin->getMetas();

        $this->assertEquals('Hello plugin', $metas['name'], 'Failed to retrieve the name of the plugin from metas');
    }
}
