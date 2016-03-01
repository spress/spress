<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\Plugin;

use Yosymfony\Spress\Core\Plugin\PluginCollection;
use Yosymfony\Spress\Core\Tester\PluginTester;

class PluginCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIterator()
    {
        $collection = new PluginCollection();
        $collection->add('acme', ($plugin1 = new PluginTester('acme')));

        $this->assertInstanceOf('\ArrayIterator', $collection->getIterator());
        $this->assertSame([
            'acme' => $plugin1,
        ], $collection->getIterator()->getArrayCopy());
    }

    public function testAll()
    {
        $collection = new PluginCollection();
        $collection->add('acme', ($plugin1 = new PluginTester('acme')));

        $this->assertSame([
            'acme' => $plugin1,
        ], $collection->all());
    }

    public function testGet()
    {
        $collection = new PluginCollection();
        $collection->add('acme', ($plugin1 = new PluginTester('acme')));

        $this->assertSame($plugin1, $collection->get('acme'));
    }

    public function testSet()
    {
        $collection = new PluginCollection();
        $collection->add('acme', new PluginTester('acme'));
        $collection->set('acme', ($plugin2 = new PluginTester('acme2')));

        $this->assertSame($plugin2, $collection->get('acme'));
    }

    public function testHas()
    {
        $collection = new PluginCollection();
        $collection->add('acme', ($plugin1 = new PluginTester('acme')));

        $this->assertTrue($collection->has('acme'));
    }

    public function testCount()
    {
        $collection = new PluginCollection();
        $collection->add('acme', ($plugin1 = new PluginTester('acme')));

        $this->assertCount(1, $collection);
    }

    public function testRemove()
    {
        $collection = new PluginCollection();
        $collection->add('acme', ($plugin1 = new PluginTester('acme')));
        $collection->add('acme2', new PluginTester('acme2'));

        $this->assertCount(2, $collection);

        $collection->remove('acme2');

        $this->assertCount(1, $collection);
    }

    public function testClear()
    {
        $collection = new PluginCollection();
        $collection->add('acme', ($plugin1 = new PluginTester('acme')));
        $collection->clear();

        $this->assertCount(0, $collection);
    }
}
