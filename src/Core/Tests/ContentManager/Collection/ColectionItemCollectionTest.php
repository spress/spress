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

use Yosymfony\Spress\Core\ContentManager\Collection\Collection;
use Yosymfony\Spress\Core\ContentManager\Collection\ColectionItemCollection;

class ColectionItemCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIterator()
    {
        $collection = new ColectionItemCollection();
        $collection->add(($collection1 = new Collection('events', 'events', [])));

        $this->assertInstanceOf('\ArrayIterator', $collection->getIterator());
        $this->assertSame([
            'events' => $collection1,
        ], $collection->getIterator()->getArrayCopy());
    }

    public function testAll()
    {
        $collection = new ColectionItemCollection();
        $collection->add(($collection1 = new Collection('events', 'events', [])));

        $this->assertSame([
            'events' => $collection1,
        ], $collection->all());
    }

    public function testGet()
    {
        $collection = new ColectionItemCollection();
        $collection->add(($collection1 = new Collection('events', 'events', [])));

        $this->assertSame($collection1, $collection->get('events'));
    }

    public function testSet()
    {
        $collection = new ColectionItemCollection();
        $collection->add(new Collection('events', 'events', []));
        $collection->set(($collection2 = new Collection('projects', 'projects', [])));

        $this->assertSame($collection2, $collection->get('projects'));
    }

    public function testHas()
    {
        $collection = new ColectionItemCollection();
        $collection->add(($collection1 = new Collection('events', 'events', [])));

        $this->assertTrue($collection->has('events'));
    }

    public function testCount()
    {
        $collection = new ColectionItemCollection();
        $collection->add(($collection1 = new Collection('events', 'events', [])));

        $this->assertCount(1, $collection);
    }

    public function testRemove()
    {
        $collection = new ColectionItemCollection();
        $collection->add(new Collection('events', 'events', []));
        $collection->add(new Collection('projects', 'projects', []));

        $this->assertCount(2, $collection);

        $collection->remove('projects');

        $this->assertCount(1, $collection);
    }

    public function testClear()
    {
        $collection = new ColectionItemCollection();
        $collection->add(new Collection('events', 'events', []));
        $collection->clear();

        $this->assertCount(0, $collection);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetCollectionNotFound()
    {
        $collection = new ColectionItemCollection();
        $collection->add(new Collection('events', 'events', []));

        $collection->get('books');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddExistingCollection()
    {
        $collection = new ColectionItemCollection();
        $collection->add(new Collection('pages', 'pages', []));
        $collection->add(new Collection('pages', 'pages', []));
    }
}
