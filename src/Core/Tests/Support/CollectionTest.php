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

use Yosymfony\Spress\Core\Support\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIterator()
    {
        $collection = new Collection();
        $collection->add('acme', 'value');

        $this->assertInstanceOf('\ArrayIterator', $collection->getIterator());
        $this->assertSame([
            'acme' => 'value',
        ], $collection->getIterator()->getArrayCopy());
    }

    public function testAll()
    {
        $collection = new Collection();
        $collection->add('acme', 'value');

        $this->assertSame([
            'acme' => 'value',
        ], $collection->all());
    }

    public function testGet()
    {
        $collection = new Collection();
        $collection->add('acme', 'value');

        $this->assertSame('value', $collection->get('acme'));
    }

    public function testSet()
    {
        $collection = new Collection();
        $collection->add('acme', 'value');
        $collection->set('acme', 'value 2');

        $this->assertSame('value 2', $collection->get('acme'));
    }

    public function testHas()
    {
        $collection = new Collection();
        $collection->add('acme', 'value');

        $this->assertTrue($collection->has('acme'));
        $this->assertFalse($collection->has('acme 2'));
    }

    public function testKeys()
    {
        $collection = new Collection();
        $collection->add('acme', 'value');
        $collection->set('acme2', 'value 2');

        $this->assertSame(['acme', 'acme2'], $collection->keys());
    }

    public function testCount()
    {
        $collection = new Collection();
        $collection->add('acme', 'value');

        $this->assertCount(1, $collection);
    }

    public function testRemove()
    {
        $collection = new Collection();
        $collection->add('acme', 'value');
        $collection->add('acme2', 'value 2');

        $this->assertCount(2, $collection);

        $collection->remove('acme2');

        $this->assertCount(1, $collection);
    }

    public function testClear()
    {
        $collection = new Collection();
        $collection->add('acme', 'value');
        $collection->clear();

        $this->assertCount(0, $collection);
    }

    /**
     * @expectedException \RuntimeException
     * expectedExceptionMessage Element with key: "page1.md" not found.
     */
    public function testElementNotFound()
    {
        $itemSet = new Collection();
        $itemSet->get('page1.md');
    }
}
