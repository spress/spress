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
use Yosymfony\Spress\Core\DataWriter\MemoryDataWriter;

class MemoryDataWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteItem()
    {
        $item = new Item('Test content', 'my-id');
        $item->setPath('index.html', Item::SNAPSHOT_PATH_PERMALINK);
        $item->setPath('index.html', Item::SNAPSHOT_PATH_RELATIVE_AFTER_CONVERT);

        $dw = new MemoryDataWriter();
        $dw->write($item);

        $this->assertTrue($dw->hasItem('index.html'));
        $this->assertEquals(1, $dw->countItems());
        $this->assertEquals('Test content', $dw->getItem('index.html')->getContent());
        $this->assertCount(1, $dw->getItems());

        $this->assertFalse($dw->hasItem('not-found-path.html'));
    }
}
