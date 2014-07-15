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

use Yosymfony\Spress\Core\Application;
use Yosymfony\Spress\Core\Plugin\PluginManager;

class PluginManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $pluginManager;
    
    public function setUp()
    {
        $this->app = new Application();
        $this->app['spress.config']->loadLocal(__DIR__ . '/../fixtures/project');
        $this->pluginManager = $this->app['spress.cms.plugin'];
    }
    
    public function testGetPlugins()
    {
        $plugins = $this->pluginManager->getPlugins();
        $this->assertTrue(is_array($plugins));
    }
    
    public function testGetHistoryEventsDispatched()
    {
        $this->pluginManager->dispatchEvent('spress.test_event');
        $events = $this->pluginManager->getHistoryEventsDispatched();
        
        $this->assertTrue(is_array($events));
        $this->assertEquals('spress.test_event', $events[0]);
    }
}