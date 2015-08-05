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

use Dflydev\EmbeddedComposer\Core\EmbeddedComposerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Yosymfony\Spress\Core\Plugin\PluginManagerBuilder;

class PluginManagerBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected $finder;
    protected $embeddedComposer;

    public function setUp()
    {
        $dir = __DIR__.'/../fixtures/project/src/plugins';
        $vendorDir = __DIR__.'/../fixtures/project/vendor';

        $this->finder = new Finder();
        $this->finder->files()
            ->in($dir)
            ->name('/(\.php|composer\.json)$/');

        $autoloaders = spl_autoload_functions();

        $embeddedComposerBuilder = new EmbeddedComposerBuilder($autoloaders[0][0]);
        $this->embeddedComposer = $embeddedComposerBuilder
            ->setComposerFilename('composer.json')
            ->setVendorDirectory($vendorDir)
            ->build();
        $this->embeddedComposer->processAdditionalAutoloads();
    }

    public function testBuild()
    {
        $builder = new PluginManagerBuilder($this->finder, new EventDispatcher());
        $pm = $builder->build();

        $this->assertEquals(2, $pm->countPlugins());

        $plugin = $pm->getPlugin('Test plugin');

        $this->assertInstanceOf('Yosymfony\Spress\Core\Plugin\PluginInterface', $plugin);

        $metas = $plugin->getMetas();

        $this->assertEquals('Test plugin', $metas['name']);

        $plugin = $pm->getPlugin('Hello plugin');

        $this->assertInstanceOf('Yosymfony\Spress\Core\Plugin\PluginInterface', $plugin);

        $metas = $plugin->getMetas();

        $this->assertEquals('Hello plugin', $metas['name']);
    }
}
