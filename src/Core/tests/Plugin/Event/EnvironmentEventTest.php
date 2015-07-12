<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\tests\Plugin\Event;

use Yosymfony\Spress\Core\Plugin\Event\EnvironmentEvent;

class EnvironmentEventTest extends \PHPUnit_Framework_TestCase
{
    public function testEnvironmentEvent()
    {
        $dsm = $this->getMockBuilder('\Yosymfony\Spress\Core\DataSource\DataSourceManager')
                     ->getMock();
        $cm = $this->getMockBuilder('\Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager')
                     ->getMock();
        $renderizer = $this->getMockBuilder('\Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface')
                     ->getMock();
        $io = $this->getMockBuilder('\Yosymfony\Spress\Core\IO\IOInterface')
                     ->getMock();

        $configValues = ['name' => 'Yo! Symfony'];

        $event = new EnvironmentEvent(
            $dsm,
            $cm,
            $renderizer,
            $io,
            $configValues);

        $this->assertInstanceOf('\Yosymfony\Spress\Core\DataSource\DataSourceManager', $event->getDataSourceManager());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager', $event->getConverterManager());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface', $event->getRenderizer());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\IO\IOInterface', $event->getIO());

        $values = $event->getConfigValues();

        $this->assertTrue(is_array($values));
        $this->assertArrayHasKey('name', $values);
        $this->assertEquals('Yo! Symfony', $values['name']);

        $values['title'] = 'My blog page';

        $event->setConfigValues($values);

        $this->assertArrayHasKey('name', $configValues);
        $this->assertEquals('My blog page', $configValues['title']);
    }
}
