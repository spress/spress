<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\DataSource\Memory;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\DataSource\Memory\MemoryDataSource;

class MemoryDataSourceTest extends TestCase
{
    public function testMemoryDataSource()
    {
        $memoryDataSource = new MemoryDataSource();
        $memoryDataSource->addItem(new Item('Content', 'index.html'));
        $memoryDataSource->addLayout(new Item('<div>{{ page.content }}</div>', 'layouts/default.html'));
        $memoryDataSource->addInclude(new Item('<p>Hi {{ name }}', 'includes/name.html'));

        $this->assertCount(1, $memoryDataSource->getItems());
        $this->assertCount(1, $memoryDataSource->getLayouts());
        $this->assertCount(1, $memoryDataSource->getIncludes());

        $this->assertEquals(1, $memoryDataSource->countItem());
        $this->assertEquals(1, $memoryDataSource->countLayout());
        $this->assertEquals(1, $memoryDataSource->countInclude());

        $this->assertTrue($memoryDataSource->hasItem('index.html'));
        $this->assertTrue($memoryDataSource->hasLayout('layouts/default.html'));
        $this->assertTrue($memoryDataSource->hasInclude('includes/name.html'));

        $memoryDataSource->removeItem('index.html');
        $memoryDataSource->removeLayout('layouts/default.html');
        $memoryDataSource->removeInclude('includes/name.html');

        $this->assertEquals(0, $memoryDataSource->countItem());
        $this->assertEquals(0, $memoryDataSource->countLayout());
        $this->assertEquals(0, $memoryDataSource->countInclude());

        $memoryDataSource->setItem(new Item('Content', 'index.html'));
        $memoryDataSource->setLayout(new Item('<div>{{ page.content }}</div>', 'layouts/default.html'));
        $memoryDataSource->setInclude(new Item('<p>Hi {{ name }}', 'includes/name.html'));

        $this->assertCount(1, $memoryDataSource->getItems());
        $this->assertCount(1, $memoryDataSource->getLayouts());
        $this->assertCount(1, $memoryDataSource->getIncludes());

        $this->assertEquals(1, $memoryDataSource->countItem());
        $this->assertEquals(1, $memoryDataSource->countLayout());
        $this->assertEquals(1, $memoryDataSource->countInclude());

        $memoryDataSource->clearItem();
        $memoryDataSource->clearLayout();
        $memoryDataSource->clearInclude();

        $this->assertCount(0, $memoryDataSource->getItems());
        $this->assertCount(0, $memoryDataSource->getLayouts());
        $this->assertCount(0, $memoryDataSource->getIncludes());

        $this->assertEquals(0, $memoryDataSource->countItem());
        $this->assertEquals(0, $memoryDataSource->countLayout());
        $this->assertEquals(0, $memoryDataSource->countInclude());

        $memoryDataSource->setItem(new Item('Content', 'index.html'));
        $memoryDataSource->setLayout(new Item('<div>{{ page.content }}</div>', 'layouts/default.html'));
        $memoryDataSource->setInclude(new Item('<p>Hi {{ name }}', 'includes/name.html'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddExistingItem()
    {
        $item = new Item('Content', 'index.html');

        $memoryDataSource = new MemoryDataSource();
        $memoryDataSource->addItem($item);
        $memoryDataSource->addItem($item);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddExistingLayout()
    {
        $item = new Item('<div>{{ page.content }}</div>', 'layouts/default.html');

        $memoryDataSource = new MemoryDataSource();
        $memoryDataSource->addItem($item);
        $memoryDataSource->addItem($item);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddExistingInclude()
    {
        $item = new Item('<p>Hi {{ name }}', 'includes/name.html');

        $memoryDataSource = new MemoryDataSource();
        $memoryDataSource->addItem($item);
        $memoryDataSource->addItem($item);
    }
}
