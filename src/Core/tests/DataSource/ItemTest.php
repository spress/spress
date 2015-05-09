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

class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultContent()
    {
        $item = new Item('Test of content', '/index.html', []);

        $this->assertEquals('/index.html', $item->getId());
        $this->assertEquals(item::TYPE_ITEM, $item->getType());
        $this->assertEquals('Test of content', $item->getContent());
        $this->assertEquals('Test of content', $item->getContent(Item::SNAPSHOT_RAW));
        $this->assertCount(0, $item->getAttributes());
        $this->assertFalse($item->isBinary());
    }

    public function testMultipleSnapshot()
    {
        $item = new Item('Raw content', '/index.html', []);

        $this->assertEquals('Raw content', $item->getContent(Item::SNAPSHOT_RAW));

        $item->setContent('After convert content', Item::SNAPSHOT_AFTER_CONVERT);

        $this->assertEquals('After convert content', $item->getContent());
        $this->assertEquals('After convert content', $item->getContent(Item::SNAPSHOT_AFTER_CONVERT));
        $this->assertEquals('Raw content', $item->getContent(Item::SNAPSHOT_RAW));
    }

    public function testBinaryContent()
    {
        $item = new Item('Binary content', '/index.html', [], true);

        $this->assertEquals('Binary content', $item->getContent());
        $this->assertTrue($item->isBinary());
    }

    public function testLayoutItem()
    {
        $item = new Item('Test of content', '/index.html', [], false, Item::TYPE_LAYOUT);

        $this->assertEquals(item::TYPE_LAYOUT, $item->getType());
    }

    public function testAttributes()
    {
        $item = new Item('Test of content', '/index.html', ['name' => 'test']);

        $this->assertCount(1, $item->getAttributes());

        $item->setAttributes([
            'attr1' => 'text 1',
            'attr2' => 'text 2',
        ]);

        $this->assertCount(2, $item->getAttributes());
    }

    public function testSnapshotNotFound()
    {
        $item = new Item('Raw content', '/index.html', []);

        $this->assertEquals('', $item->getContent('InventedSnapshot'));
    }

    public function testSetPath()
    {
        $item = new Item('Raw content', '/index.html', []);
        $item->setPath('index.html', Item::SNAPSHOT_PATH_RELATIVE);
        $item->setPath('/index.html', Item::SNAPSHOT_PATH_PERMALINK);

        $this->assertEquals('/index.html', $item->getPath());
        $this->assertEquals('/index.html', $item->getPath(Item::SNAPSHOT_PATH_PERMALINK));
        $this->assertEquals('index.html', $item->getPath(Item::SNAPSHOT_PATH_RELATIVE));
    }
}
