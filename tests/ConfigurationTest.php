<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Tests;

use Yosymfony\Spress\Application;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $config;
    
    public function setUp()
    {
        $this->app = new Application();
        $this->config = $this->app['spress.config'];
        $this->config->loadLocal('./tests/fixtures/project');
    }
    
    public function testConfiguration()
    {   
        $this->assertInstanceOf('Yosymfony\Spress\Configuration', $this->app['spress.config']);
    }
    
    public function testLoadConfigurations()
    {
        $this->assertTrue(count($this->config->getRepository()) > 0);
        $this->assertTrue(count($this->config->getGlobal()) > 0);
        $this->assertTrue(count($this->config->getLocal()) > 0);
    }
    
    /*public function testLoadLocalConfigurationWithDoubleSlash()
    {
        $this->config->loadLocal('./tests/fixtures/project/');
        
        $this->assertTrue(count($this->config->getRepository()) > 0);
        $this->assertTrue(count($this->config->getGlobal()) > 0);
        $this->assertTrue(count($this->config->getLocal()) > 0);
    }*/
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadLocalConfigurationFail()
    {
        $this->config->loadLocal('/not/exists/directory/config.toml');
    }
    
    public function testConfigurationInline()
    {
        $repositoryA = $this->config->getRepositoryInline('template: template-inline-test');
        $repositoryC = $repositoryA->mergeWith($this->config->getRepository());
        
        $this->assertEquals('template-inline-test', $repositoryC['template']);
    }
    
    public function testCreateBlankRepository()
    {
        $repository = $this->config->createBlankRepository();
        
        $this->assertInstanceOf('Yosymfony\Silex\ConfigServiceProvider\ConfigRepository', $repository);
        $this->assertCount(0, $repository);
    }
    
    public function testConfigurationInlineEmpty()
    {
        $repository = $this->config->getRepositoryInline('');
        
        $this->assertCount(0, $repository);
    }
    
    public function testConfigurationInlineNull()
    {
        $repository = $this->config->getRepositoryInline(null);
        
        $this->assertCount(0, $repository);
    }
}