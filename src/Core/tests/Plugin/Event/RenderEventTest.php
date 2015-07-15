<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\tests\Plugin\Event;

use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\Plugin\Event\RenderEvent;

class RenderEventTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEvent()
    {
        $item = new Item('Test of content', 'index.html', ['title' => 'My posts']);
        $item->setPath('index.html', Item::SNAPSHOT_PATH_RELATIVE);

        $event = new RenderEvent($item, Item::SNAPSHOT_RAW, Item::SNAPSHOT_PATH_RELATIVE);

        $this->assertEquals('', $event->getRelativeUrl());

        $event->setRelativeUrl('/welcome/index.html');

        $this->assertEquals('/welcome/index.html', $event->getRelativeUrl());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAbsoluteUrl()
    {
        $item = new Item('Test of content', 'index.html', ['title' => 'My posts']);

        $event = new RenderEvent($item, Item::SNAPSHOT_RAW, Item::SNAPSHOT_PATH_RELATIVE);
        $event->setRelativeUrl('http://localhost/index.html');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUrlWithoutStartWithSlash()
    {
        $item = new Item('Test of content', 'index.html', ['title' => 'My posts']);

        $event = new RenderEvent($item, Item::SNAPSHOT_RAW, Item::SNAPSHOT_PATH_RELATIVE);
        $event->setRelativeUrl('index.html');
    }
}
