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

        $collection = new ItemCollection([$item1, $item2]);

        $this->assertEquals(2, $collection->count());
        $this->assertTrue($collection->has('post1.md'));
        $this->assertTrue($collection->has('post2.md'));
        $this->assertCount(2, $collection->all());
        $this->assertCount(2, $collection->all(['posts']));

        $item3 = new Item('', 'page1.md', ['number' => 1]);
        $item2->setCollection('pages');
        $collection->add($item3->getId(), $item3);

        $this->assertCount(2, $collection->all([], true));
        $this->assertArrayHasKey('posts', $collection->all([], true));
        $this->assertArrayNotHasKey('posts', $collection->all([], false));
        $this->assertArrayHasKey('pages', $collection->all([], true));

        $collection->sortItems('date', false);

        $items = $collection->all(['posts']);
        $this->assertCount(2, $items);
        $this->assertEquals('post2.md', current($items)->getId());

        $collection->sortItems('date', true);

        $items = $collection->all(['posts']);
        $this->assertCount(2, $items);
        $this->assertEquals('post1.md', current($items)->getId());

        $collection->remove('post1.md');

        $this->assertCount(2, $collection->all());
        $this->assertEquals('page1.md', $collection->get('page1.md')->getId());

        $collection->clear();

        $this->assertCount(0, $collection->all());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The item with id: "post1.md" has been registered previously with another collection.
     */
    public function testRegisterItemInSeveralCollections()
    {
        $collection = new ItemCollection();

        $item1 = new Item('', 'post1.md', ['date' => '2016-01-20']);
        $item1->setCollection('posts');

        $collection->set($item1->getId(), $item1);

        $item1->setCollection('events');

        $collection->set($item1->getId(), $item1);
    }
}
