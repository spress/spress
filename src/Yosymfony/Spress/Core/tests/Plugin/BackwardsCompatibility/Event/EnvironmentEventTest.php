<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Tests\Plugin\Event;

use Yosymfony\Spress\Core\Application;
use Yosymfony\Spress\Plugin\Event\EnvironmentEvent;

class EnvironmentEventTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $event;
    
    public function setUp()
    {
        $this->app = new Application();
        $this->app['spress.config']->loadLocal(__DIR__ . '/../../../fixtures/project');
        $this->event = new EnvironmentEvent(
            $this->app['spress.config'],
            $this->app['spress.cms.converter'],
            $this->app['spress.cms.renderizer'],
            $this->app['spress.content_locator'],
            $this->app['spress.io']
        );
    }
    
    public function testGetConfigRepository()
    {
        $this->assertInstanceOf(
            'Yosymfony\ConfigLoader\Repository',
            $this->event->getConfigRepository());
    }
    
    public function testGetTemplateManager()
    {
        $this->assertInstanceOf(
            'Yosymfony\Spress\Core\Plugin\Api\TemplateManager',
            $this->event->getTemplateManager());
    }
    
    public function testGetIO()
    {
        $this->assertInstanceOf(
            'Yosymfony\Spress\Core\IO\IOInterface',
            $this->event->getIO());
    }
    
    public function testGetSourceDir()
    { 
        $this->assertTrue(strlen($this->event->getSourceDir()) > 0);
    }
    
    public function testGetPostsDir()
    { 
        $this->assertTrue(strlen($this->event->getPostsDir()) > 0);
    }
    
    public function testGetLayoutsDir()
    { 
        $this->assertTrue(strlen($this->event->getLayoutsDir()) > 0);
    }
    
    public function testGetDestinationDir()
    { 
        $this->assertTrue(strlen($this->event->getDestinationDir()) > 0);
    }
    
    public function testGetIncludesDir()
    { 
        $this->assertTrue(strlen($this->event->getIncludesDir()) > 0);
    }
    
    public function testAddTwigFunction()
    {   
        $this->event->addTwigFunction('fTest', function($param){
            return $param;
        });
    }
    
    public function testAddTwigFunctionWithOptions()
    {   
        $this->event->addTwigFunction('fTest', function(\Twig_Environment $env, $context, $param){
            return $param;
        }, ['needs_context' => true, 'needs_environment' => true]);
    }
    
    public function testAddTwigFilter()
    {
        $this->event->addTwigFilter('fTest', function($param){
            return $param;
        });
    }
    
    public function testAddTwigFilterWithOptions()
    {   
        $this->event->addTwigFilter('fTest', function(\Twig_Environment $env, $context, $param){
            return $param;
        }, ['needs_context' => true, 'needs_environment' => true]);
    }
    
    public function testAddTwigTest()
    {   
        $this->event->addTwigFilter('tTest', function($param){
            return true;
        });
    }
}