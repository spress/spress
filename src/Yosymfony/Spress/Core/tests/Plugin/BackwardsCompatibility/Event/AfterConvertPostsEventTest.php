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

use Yosymfony\Spress\Plugin\Event\AfterConvertPostsEvent;

class AfterConvertPostsEventTest extends \PHPUnit_Framework_TestCase
{
    protected $event;
    
    public function setUp()
    {
        $this->event = new AfterConvertPostsEvent(
            ['category 1', 'category 2'],
            ['tag 1', 'tag 2']);
    }
    
    public function testGetCatetories()
    {
        $this->assertTrue(is_array($this->event->getCategories()));
        $this->assertCount(2, $this->event->getCategories());
        $this->assertContains('category 1', $this->event->getCategories());
    }
    
    public function testGetTags()
    {
        $this->assertTrue(is_array($this->event->getTags()));
        $this->assertCount(2, $this->event->getTags());
        $this->assertContains('tag 1', $this->event->getTags());
    }
}