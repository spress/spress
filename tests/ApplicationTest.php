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

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    
    public function setUp()
    {
        $this->app = new Application();
    }
    
    public function testParse()
    {
        $result = $this->app->parse('./tests/fixtures/project');
        
        $this->assertTrue(is_array($result));
        $this->assertEquals(4, $result['total_post']);
        $this->assertEquals(2, $result['processed_post']);
        $this->assertEquals(1, $result['drafts_post']);
        $this->assertEquals(6, $result['total_pages']);
        $this->assertEquals(6, $result['processed_pages']);
        $this->assertEquals(3, $result['other_resources']);
    }
    
    /**
     * @link http://php.net/manual/en/timezones.php
     */
    public function testParseTimezone()
    {
        $result = $this->app->parse('./tests/fixtures/project', 'Europe/Madrid');
        
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
        $result = $this->app->parse('./tests/fixtures/project', null, true);
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
        $result = $this->app->parse('./tests/fixtures/project', null, null, true);
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