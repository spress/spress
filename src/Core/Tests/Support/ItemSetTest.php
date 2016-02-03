<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\Support;

use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\Support\ItemSet;

class ItemSetTest extends \PHPUnit_Framework_TestCase
{
    public function testItemSet()
    {
        $item1 = new Item('', 'post1.md', ['date' => '2016-01-20']);
        $item1->setCollection('posts');

        $item2 = new Item('', 'post2.md', ['order' => '2016-01-19']);
        $item2->setCollection('posts');

        $itemSet = new ItemSet([$item1, $item2]);

        $this->assertEquals(2, $itemSet->countItem());
        $this->assertTrue($itemSet->hasItem('post1.md'));
        $this->assertTrue($itemSet->hasItem('post2.md'));
        $this->assertCount(2, $itemSet->getItems());
        $this->assertCount(2, $itemSet->getItems(['posts']));

        $item3 = new Item('', 'page1.md', ['number' => 1]);
        $item2->setCollection('pages');
        $itemSet->addItem($item3);

        $this->assertCount(2, $itemSet->getItems([], true));
        $this->assertArrayHasKey('posts', $itemSet->getItems([], true));
        $this->assertArrayNotHasKey('posts', $itemSet->getItems([], false));
        $this->assertArrayHasKey('pages', $itemSet->getItems([], true));

        $itemSet->sortItems('date', false);

        $items = $itemSet->getItems(['posts']);
        $this->assertCount(2, $items);
        $this->assertEquals('post2.md', current($items)->getId());

        $itemSet->sortItems('date', true);

        $items = $itemSet->getItems(['posts']);
        $this->assertCount(2, $items);
        $this->assertEquals('post1.md', current($items)->getId());

        $itemSet->removeItem('post1.md');

        $this->assertCount(2, $itemSet->getItems());
        $this->assertEquals('page1.md', $itemSet->getItem('page1.md')->getId());

        $itemSet->clearItem();

        $this->assertCount(0, $itemSet->getItems());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testItemNotFound()
    {
        $itemSet = new ItemSet();
        $itemSet->getItem('page1.md');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotUniqueId()
    {
        $item1 = new Item('', 'post1.md', ['date' => '2016-01-20']);
        $item1->setCollection('posts');

        $item2 = new Item('', 'post1.md', ['order' => '2016-01-19']);
        $item2->setCollection('posts');

        $itemSet = new ItemSet([$item1, $item2]);
    }
}
