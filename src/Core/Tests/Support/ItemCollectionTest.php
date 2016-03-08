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
use Yosymfony\Spress\Core\Support\ItemCollection;

class ItemCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testItemSet()
    {
        $item1 = new Item('', 'post1.md', ['date' => '2016-01-20']);
        $item1->setCollection('posts');

        $item2 = new Item('', 'post2.md', ['order' => '2016-01-19']);
        $item2->setCollection('posts');

        $itemSet = new ItemCollection([$item1, $item2]);

        $this->assertEquals(2, $itemSet->count());
        $this->assertTrue($itemSet->has('post1.md'));
        $this->assertTrue($itemSet->has('post2.md'));
        $this->assertCount(2, $itemSet->all());
        $this->assertCount(2, $itemSet->all(['posts']));

        $item3 = new Item('', 'page1.md', ['number' => 1]);
        $item2->setCollection('pages');
        $itemSet->add($item3);

        $this->assertCount(2, $itemSet->all([], true));
        $this->assertArrayHasKey('posts', $itemSet->all([], true));
        $this->assertArrayNotHasKey('posts', $itemSet->all([], false));
        $this->assertArrayHasKey('pages', $itemSet->all([], true));

        $itemSet->sortItems('date', false);

        $items = $itemSet->all(['posts']);
        $this->assertCount(2, $items);
        $this->assertEquals('post2.md', current($items)->getId());

        $itemSet->sortItems('date', true);

        $items = $itemSet->all(['posts']);
        $this->assertCount(2, $items);
        $this->assertEquals('post1.md', current($items)->getId());

        $itemSet->remove('post1.md');

        $this->assertCount(2, $itemSet->all());
        $this->assertEquals('page1.md', $itemSet->get('page1.md')->getId());

        $itemSet->clear();

        $this->assertCount(0, $itemSet->all());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testItemNotFound()
    {
        $itemSet = new ItemCollection();
        $itemSet->get('page1.md');
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

        $itemSet = new ItemCollection([$item1, $item2]);
    }
}
