<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\Plugin;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Yosymfony\Spress\Core\Plugin\PluginManager;

class PluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testPluginManager()
    {
        $pm = new PluginManager(new EventDispatcher());

        $plugin1 = $this->getMockBuilder('\Yosymfony\Spress\Core\Plugin\PluginInterface')->getMock();
        $plugin2 = $this->getMockBuilder('\Yosymfony\Spress\Core\Plugin\PluginInterface')->getMock();

        $plugin1->expects($this->once())
            ->method('initialize');

        $plugin2->expects($this->once())
            ->method('initialize');

        $pm->addPlugin('plugin1', $plugin1);
        $pm->setPlugin('plugin2', $plugin2);

        $this->assertEquals(2, $pm->countPlugins());
        $this->assertTrue($pm->hasPlugin('plugin1'));
        $this->assertFalse($pm->hasPlugin('plugin3'));
        $this->assertInstanceOf('\Yosymfony\Spress\Core\Plugin\PluginInterface', $pm->getPlugin('plugin1'));

        $pm->callInitialize();

        $pm->removePlugin('plugin1');

        $this->assertEquals(1, $pm->countPlugins());

        $pm->clearPlugin();

        $this->assertEquals(0, $pm->countPlugins());
    }
}
