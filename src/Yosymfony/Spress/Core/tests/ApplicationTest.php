<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Core\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    
    public function setUp()
    {
        $this->app = new Application();
    }
    
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove(['./tests/out', './tests/fixtures/project/_site']);
    }
    
    public function testParse()
    {
        $result = $this->app->parse('./tests/fixtures/project');
        $config = $this->app['spress.config'];
        
        $this->assertTrue(is_array($result));
        $this->assertEquals(4, $result['total_post']);
        $this->assertEquals(2, $result['processed_post']);
        $this->assertEquals(1, $result['drafts_post']);
        $this->assertEquals(6, $result['total_pages']);
        $this->assertEquals(6, $result['processed_pages']);
        $this->assertEquals(3, $result['other_resources']);
        
        $this->assertEquals('dev', $config->getEnvironmentName());
    }
    
    public function testParseProdEnvironment()
    {
        $result = $this->app->parse('./tests/fixtures/project', 'prod');
        $config = $this->app['spress.config'];
        
        $this->assertTrue(is_array($result));
        $this->assertEquals(4, $result['total_post']);
        $this->assertEquals(2, $result['processed_post']);
        $this->assertEquals(1, $result['drafts_post']);
        $this->assertEquals(6, $result['total_pages']);
        $this->assertEquals(6, $result['processed_pages']);
        $this->assertEquals(3, $result['other_resources']);
        
        $this->assertEquals('prod', $config->getEnvironmentName());
    }
    
    /**
     * @link http://php.net/manual/en/timezones.php
     */
    public function testParseTimezone()
    {
        $result = $this->app->parse('./tests/fixtures/project', 'dev', 'Europe/Madrid');
        
        $this->assertEquals('Europe/Madrid', date_default_timezone_get()); 
        $this->assertTrue(is_array($result));
        $this->assertEquals(4, $result['total_post']);
        $this->assertEquals(2, $result['processed_post']);
        $this->assertEquals(1, $result['drafts_post']);
        $this->assertEquals(6, $result['total_pages']);
        $this->assertEquals(6, $result['processed_pages']);
        $this->assertEquals(3, $result['other_resources']);
    }
    
    public function testParseDraft()
    {
        $result = $this->app->parse('./tests/fixtures/project', 'dev', null, true);
        $repository = $this->app['spress.config']->getRepository();
        
        $this->assertTrue(true, $repository->get('drafts'));
        
        $this->assertTrue(is_array($result));
        $this->assertEquals(4, $result['total_post']);
        $this->assertEquals(3, $result['processed_post']);
        $this->assertEquals(0, $result['drafts_post']);
        $this->assertEquals(6, $result['total_pages']);
        $this->assertEquals(6, $result['processed_pages']);
        $this->assertEquals(3, $result['other_resources']);
    }
    
    public function testParseSafe()
    {
        $result = $this->app->parse('./tests/fixtures/project', 'dev', null, null, true);
        $repository = $this->app['spress.config']->getRepository();

        $this->assertTrue(true, $repository->get('safe'));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseLocalPathFail()
    {
        $this->app->parse('./tests/fixtures/project-not-exists');
    }
}