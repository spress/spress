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

use Symfony\Component\EventDispatcher\EventDispatcher;
use Yosymfony\Spress\Core\Plugin\PluginManager;

class PluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPluginCollection()
    {
        $pm = new PluginManager(new EventDispatcher());
        $this->assertInstanceOf('Yosymfony\Spress\Core\Support\Collection', $pm->getPluginCollection());
    }

    public function testCallInitialize()
    {
        $pm = new PluginManager(new EventDispatcher());

        $plugin1 = $this->getMockBuilder('\Yosymfony\Spress\Core\Plugin\PluginInterface')->getMock();
        $plugin2 = $this->getMockBuilder('\Yosymfony\Spress\Core\Plugin\PluginInterface')->getMock();

        $plugin1->expects($this->once())
            ->method('initialize');

        $plugin2->expects($this->once())
            ->method('initialize');

        $pluginCollection = $pm->getPluginCollection();

        $pluginCollection->add('plugin1', $plugin1);
        $pluginCollection->add('plugin2', $plugin2);

        $pm->callInitialize();
    }
}
