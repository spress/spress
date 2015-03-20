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

use Yosymfony\Spress\Core\Plugin\PluginItem;

class PluginItemTest extends \PHPUnit_Framework_TestCase
{
    protected $pluginMock;
    protected $pluginMockNoMetas;

    public function setUp()
    {
        $this->pluginMock = $this->getMockBuilder('Yosymfony\\Spress\\Core\\Plugin\\Plugin')
            ->getMock();

        $this->pluginMockNoMetas = $this->getMockBuilder('Yosymfony\\Spress\\Core\\Plugin\\Plugin')
            ->getMock();

        $this->pluginMock->expects($this->any())
            ->method('getMetas')
            ->will($this->returnValue([
                'name' => 'testPlugin',
                'author' => 'Yo! Symfony',
            ]));
    }

    public function testGetPugin()
    {
        $pluginItem = new PluginItem($this->pluginMock);

        $this->assertInstanceOf('Yosymfony\\Spress\\Core\\Plugin\\PluginInterface', $pluginItem->getPlugin());
    }

    public function testGetName()
    {
        $pluginItem = new PluginItem($this->pluginMock);

        $this->assertEquals('testPlugin', $pluginItem->getName());
    }

    public function testGetAuthor()
    {
        $pluginItem = new PluginItem($this->pluginMock);

        $this->assertEquals('Yo! Symfony', $pluginItem->getAuthor());
    }

    public function testGetNameEmpty()
    {
        $pluginItem = new PluginItem($this->pluginMockNoMetas);

        $this->assertTrue(strlen($pluginItem->getName()) > 0);
    }

    public function testGetAuthorEmpty()
    {
        $pluginItem = new PluginItem($this->pluginMockNoMetas);

        $this->assertEquals('', $pluginItem->getAuthor());
    }
}
