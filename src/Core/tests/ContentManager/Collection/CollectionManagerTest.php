<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\ContentManager\Collection;

use Yosymfony\Spress\Core\ContentManager\Collection\Collection;
use Yosymfony\Spress\Core\ContentManager\Collection\CollectionManager;
use Yosymfony\Spress\Core\DataSource\Item;

class CollectionManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCollectionForItems()
    {
        $cm = new CollectionManager();
        $cm->add(new Collection('events', '_events', ['output' => true]));
        $cm->add(new Collection('books', '_books', ['output' => true]));

        $item = new Item('Test of content', '_events/event-1.html', []);
        $item->setPath('_events/event-1.html');
        $collection = $cm->getCollectionForItem($item);

        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface', $collection);
        $this->assertEquals('events', $collection->getName());
        $this->assertEquals('_events', $collection->getPath());
        $this->assertCount(1, $collection->getAttributes());

        $item = new Item('Test of content', '_books/symfony-book.html', []);
        $item->setPath('_books/symfony-book.html');
        $collection = $cm->getCollectionForItem($item);

        $this->assertEquals('books', $collection->getName());
    }

    public function testManageCollection()
    {
        $cm = new CollectionManager();
        $cm->add(new Collection('events', '_events', ['output' => true]));
        $cm->add(new Collection('books', '_books', ['output' => true]));

        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface', $cm->get('events'));
        $this->assertTrue($cm->has('events'));
        $this->assertEquals(2, $cm->count());

        $cm->remove('events');

        $this->assertEquals(1, $cm->count());

        $cm->clear();

        $this->assertEquals(0, $cm->count());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetCollectionNotFound()
    {
        $cm = new CollectionManager();
        $cm->add(new Collection('events', '_events', ['output' => true]));

        $cm->get('books');
    }
}
