<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\ContentManager\Collection;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\ContentManager\Collection\Collection;
use Yosymfony\Spress\Core\ContentManager\Collection\CollectionManager;
use Yosymfony\Spress\Core\DataSource\Item;

class CollectionManagerTest extends TestCase
{
    public function testGetCollectionItemCollection()
    {
        $cm = new CollectionManager();
        $this->assertInstanceOf('Yosymfony\Spress\Core\Support\Collection', $cm->getCollectionItemCollection());
    }

    public function testGetCollectionForItems()
    {
        $cm = new CollectionManager();
        $cm->getCollectionItemCollection()->add('events', new Collection('events', 'events'));
        $cm->getCollectionItemCollection()->add('books', new Collection('books', 'books'));

        $item = new Item('Test of content', 'events/event-1.html');
        $item->setPath('events/event-1.html', Item::SNAPSHOT_PATH_RELATIVE);
        $collection = $cm->getCollectionForItem($item);

        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface', $collection);
        $this->assertEquals('events', $collection->getName());
        $this->assertEquals('events', $collection->getPath());
        $this->assertCount(0, $collection->getAttributes());
    }

    public function testDefaultCollection()
    {
        $cm = new CollectionManager();

        $this->assertCount(1, $cm->getCollectionItemCollection());

        $item = new Item('Test of content', 'member-1.html');
        $item->setPath('member-1.html', Item::SNAPSHOT_PATH_RELATIVE);
        $collection = $cm->getCollectionForItem($item);

        $this->assertEquals('pages', $collection->getName());
        $this->assertEquals('', $collection->getPath());
    }
}
