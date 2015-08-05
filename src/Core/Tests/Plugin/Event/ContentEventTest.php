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

use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\Plugin\Event\ContentEvent;

class ContentEventTest extends \PHPUnit_Framework_TestCase
{
    public function testContentEvent()
    {
        $item = new Item('Test of content', 'index.html', ['title' => 'My posts']);
        $item->setPath('index.html', Item::SNAPSHOT_PATH_RELATIVE);

        $event = new ContentEvent($item, Item::SNAPSHOT_RAW, Item::SNAPSHOT_PATH_RELATIVE);

        $this->assertEquals('index.html', $event->getId());
        $this->assertEquals('Test of content', $event->getContent());
        $this->assertTrue(is_array($event->getAttributes()));
        $this->assertCount(1, $event->getAttributes());
        $this->assertArrayHasKey('title', $event->getAttributes());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\DataSource\ItemInterface', $event->getItem());

        $attributes = $event->getAttributes();
        $attributes['name'] = 'Yo! Symtony';
        $event->setAttributes($attributes);

        $this->assertCount(2, $event->getAttributes());
        $this->assertArrayHasKey('name', $event->getAttributes());

        $event->setContent('New content');
        $this->assertEquals('New content', $event->getContent());
        $this->assertEquals('New content', $event->getItem()->getContent(Item::SNAPSHOT_RAW));
    }
}
