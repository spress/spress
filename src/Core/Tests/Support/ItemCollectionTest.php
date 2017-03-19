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

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\Support\ItemCollection;

class ItemCollectionTest extends TestCase
{
    public function testIterator()
    {
        $item = new Item('', 'post1.md');
        $item->setCollection('posts');

        $collection = new ItemCollection([$item]);

        $this->assertInstanceOf('\ArrayIterator', $collection->getIterator());
        $this->assertSame([
            'post1.md' => $item,
        ], $collection->getIterator()->getArrayCopy());
    }

    public function testHas()
    {
        $item = new Item('', 'post1.md');
        $item->setCollection('posts');

        $collection = new ItemCollection([$item]);

        $this->assertTrue($collection->has('post1.md'));
        $this->assertFalse($collection->has('post2.md'));
    }

    public function testAdd()
    {
        $collection = new ItemCollection();

        $item = new Item('', 'post1.md');
        $item->setCollection('posts');

        $collection->add($item);

        $this->assertTrue($collection->has('post1.md'));
    }

    public function testCount()
    {
        $collection = new ItemCollection();

        $this->assertCount(0, $collection);
        $this->assertEquals(0, $collection->count());

        $item = new Item('', 'post1.md');
        $item->setCollection('posts');

        $collection->add($item);

        $this->assertCount(1, $collection);
        $this->assertEquals(1, $collection->count());
    }

    public function testGet()
    {
        $collection = new ItemCollection();

        $item = new Item('', 'post1.md');
        $item->setCollection('posts');

        $collection->add($item);

        $this->assertSame($item, $collection->get('post1.md'));
    }

    public function testSet()
    {
        $item1 = new Item('', 'post1.md', ['date' => '2016-01-20']);
        $item1->setCollection('posts');

        $collection = new ItemCollection([$item1]);

        $this->assertSame($item1, $collection->get('post1.md'));

        $item2 = new Item('', 'post1.md', ['date' => '2016-01-19']);
        $item2->setCollection('posts');

        $collection->set($item2);

        $this->assertSame($item2, $collection->get('post1.md'));
    }

    public function testAll()
    {
        $item = new Item('', 'post1.md');
        $item->setCollection('posts');

        $collection = new ItemCollection([$item]);

        $this->assertSame([
            'post1.md' => $item,
        ], $collection->all());
    }

    public function testAllWithCollections()
    {
        $item1 = new Item('', 'post1.md');
        $item1->setCollection('posts');

        $item2 = new Item('', 'about.md');
        $item2->setCollection('pages');

        $collection = new ItemCollection([$item1, $item2]);

        $this->assertSame([
            'about.md' => $item2,
        ], $collection->all(['pages']));

        $this->assertSame([
            'post1.md' => $item1,
        ], $collection->all(['posts']));
    }

    public function testAllGroupByCollection()
    {
        $item1 = new Item('', 'post1.md');
        $item1->setCollection('posts');

        $item2 = new Item('', 'about.md');
        $item2->setCollection('pages');

        $collection = new ItemCollection([$item1, $item2]);

        $this->assertSame([
            'posts' => [
                'post1.md' => $item1,
            ],
            'pages' => [
                'about.md' => $item2,
             ],
        ], $collection->all([], true));
    }

    public function testSortItems()
    {
        $item1 = new Item('', 'post1.md', ['date' => '2016-01-20']);
        $item1->setCollection('posts');

        $item2 = new Item('', 'post2.md', ['order' => '2016-01-19']);
        $item2->setCollection('posts');

        $collection = new ItemCollection([$item1, $item2]);

        $this->assertSame([
            'post1.md' => $item1,
            'post2.md' => $item2,
        ], $collection->all());

        $collection->sortItems('date', false);

        $this->assertSame([
            'post2.md' => $item2,
            'post1.md' => $item1,
        ], $collection->all());
    }

    public function testSortOnlyCollection()
    {
        $item1 = new Item('', 'post1.md', ['date' => '2016-01-20']);
        $item1->setCollection('posts');

        $item2 = new Item('', 'post2.md', ['order' => '2016-01-19']);
        $item2->setCollection('posts');

        $item3 = new Item('', 'about.md');
        $item3->setCollection('pages');

        $collection = new ItemCollection([$item1, $item2, $item3]);

        $this->assertSame([
            'post1.md' => $item1,
            'post2.md' => $item2,
            'about.md' => $item3,
        ], $collection->all());

        $collection->sortItems('date', false, ['posts']);

        $this->assertSame([
            'post2.md' => $item2,
            'post1.md' => $item1,
            'about.md' => $item3,
        ], $collection->all());
    }

    public function testRemove()
    {
        $item1 = new Item('', 'post1.md');
        $item1->setCollection('posts');

        $item2 = new Item('', 'post2.md');
        $item2->setCollection('posts');

        $collection = new ItemCollection([$item1, $item2]);

        $this->assertCount(2, $collection);

        $collection->remove('post1.md');

        $this->assertCount(1, $collection);
        $this->assertTrue($collection->has('post2.md'));
    }

    public function testClear()
    {
        $item1 = new Item('', 'post1.md');
        $item1->setCollection('posts');

        $item2 = new Item('', 'post2.md');
        $item2->setCollection('posts');

        $collection = new ItemCollection([$item1, $item2]);

        $this->assertCount(2, $collection);

        $collection->clear();

        $this->assertCount(0, $collection);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Item with id: "page1.md" not found.
     */
    public function testItemNotFound()
    {
        $collection = new ItemCollection();
        $collection->get('page1.md');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage A previous item exists with the same id: "post1.md".
     */
    public function testNotUniqueId()
    {
        $item1 = new Item('', 'post1.md', ['date' => '2016-01-20']);
        $item1->setCollection('posts');

        $item2 = new Item('', 'post1.md', ['order' => '2016-01-19']);
        $item2->setCollection('posts');

        new ItemCollection([$item1, $item2]);
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

        $collection->set($item1);

        $item1->setCollection('events');

        $collection->set($item1);
    }
}
