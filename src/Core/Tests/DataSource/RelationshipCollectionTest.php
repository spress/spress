<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\DataSource;

use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\DataSource\RelationshipCollection;

class RelationshipCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testItem()
    {
        $collection = new RelationshipCollection();
        $collection->add('next', new Item('', 'page3.md'));
        $collection->add('prior', new Item('', 'page1.md'));

        $this->assertCount(1, $collection->get('next'));
        $this->assertArrayHasKey('page3.md', $collection->get('next'));

        $this->assertCount(1, $collection->get('prior'));
        $this->assertArrayHasKey('page1.md', $collection->get('prior'));
    }

    public function testCount()
    {
        $collection = new RelationshipCollection();
        $collection->add('related_posts', new Item('', 'page3.md'));
        $collection->add('related_posts', new Item('', 'page1.md'));

        $this->assertCount(1, $collection);
    }

    public function testIterator()
    {
        $collection = new RelationshipCollection();
        $collection->add('next', $item1 = new Item('', 'page3.md'));
        $collection->add('prior', $item2 = new Item('', 'page1.md'));

        $this->assertInstanceOf('\ArrayIterator', $collection->getIterator());
        $this->assertSame([
            'next' => ['page3.md' => $item1],
            'prior' => ['page1.md' => $item2], ], $collection->getIterator()->getArrayCopy());
    }

    public function testGet()
    {
        $collection = new RelationshipCollection();
        $collection->add('next', $item1 = new Item('', 'page3.md'));
        $collection->add('prior', $item2 = new Item('', 'page1.md'));

        $this->assertCount(1, $collection->get('next'));
        $this->assertCount(1, $collection->get('prior'));

        $this->assertSame(['page3.md' => $item1], $collection->get('next'));
        $this->assertSame(['page1.md' => $item2], $collection->get('prior'));

        $this->assertEquals([], $collection->get('not-exists'));
    }

    public function testAll()
    {
        $collection = new RelationshipCollection();
        $collection->add('next', $item1 = new Item('', 'page3.md'));
        $collection->add('prior', $item2 = new Item('', 'page1.md'));

        $this->count(2, $collection->All());

        $this->assertSame([
            'next' => ['page3.md' => $item1],
            'prior' => ['page1.md' => $item2], ], $collection->All());
    }

    public function testRemove()
    {
        $collection = new RelationshipCollection();
        $collection->add('next', $item1 = new Item('', 'page3.md'));
        $collection->add('prior', $item2 = new Item('', 'page1.md'));

        $collection->remove('next', $item1);

        $this->assertEquals(1, $collection->count());

        $this->assertSame([
            'prior' => ['page1.md' => $item2], ], $collection->All());
    }

    public function testClear()
    {
        $collection = new RelationshipCollection();
        $collection->add('next', $item1 = new Item('', 'page3.md'));
        $collection->add('prior', $item2 = new Item('', 'page1.md'));

        $collection->clear();

        $this->assertEquals(0, $collection->count());
    }
}
