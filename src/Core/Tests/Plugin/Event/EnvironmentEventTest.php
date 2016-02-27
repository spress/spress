<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\Plugin\Event;

use Yosymfony\Spress\Core\Plugin\Event\EnvironmentEvent;

class EnvironmentEventTest extends \PHPUnit_Framework_TestCase
{
    protected $event;
    protected $configValues = [];

    public function setUp()
    {
        $dsm = $this->getMockBuilder('\Yosymfony\Spress\Core\DataSource\DataSourceManager')
                     ->getMock();
        $cm = $this->getMockBuilder('\Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager')
                     ->getMock();
        $gm = $this->getMockBuilder('\Yosymfony\Spress\Core\ContentManager\Generator\GeneratorManager')
                     ->getMock();
        $renderizer = $this->getMockBuilder('\Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface')
                     ->getMock();
        $io = $this->getMockBuilder('\Yosymfony\Spress\Core\IO\IOInterface')
                     ->getMock();
        $dw = $this->getMockBuilder('\Yosymfony\Spress\Core\DataWriter\DataWriterInterface')
                     ->getMock();

        $this->configValues = ['name' => 'Yo! Symfony'];

        $this->event = new EnvironmentEvent(
            $dsm,
            $dw,
            $cm,
            $gm,
            $renderizer,
            $io,
            $this->configValues);
    }

    public function testGetObjects()
    {
        $this->assertInstanceOf('\Yosymfony\Spress\Core\DataSource\DataSourceManager', $this->event->getDataSourceManager());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\DataWriter\DataWriterInterface', $this->event->getDataWriter());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager', $this->event->getConverterManager());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Generator\GeneratorManager', $this->event->getGeneratorManager());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface', $this->event->getRenderizer());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\IO\IOInterface', $this->event->getIO());
    }

    public function testGetConfigValues()
    {
        $values = $this->event->getConfigValues();

        $this->assertTrue(is_array($values));
        $this->assertArrayHasKey('name', $values);
        $this->assertEquals('Yo! Symfony', $values['name']);
    }

    public function testSetConfigValues()
    {
        $this->event->setConfigValues(['title' => 'My blog page']);

        $this->assertArrayHasKey('title', $this->configValues);
        $this->assertEquals('My blog page', $this->configValues['title']);

        $values = $this->event->getConfigValues();

        $this->assertArrayHasKey('title', $values);
    }
}
