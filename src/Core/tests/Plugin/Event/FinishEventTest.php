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
use Yosymfony\Spress\Core\Plugin\Event\FinishEvent;

class FinishEventTest extends \PHPUnit_Framework_TestCase
{
    public function testFinishTest()
    {
        $item = new Item('Test of content', 'index.html', ['title' => 'My posts']);

        $items = [$item];
        $siteAttributes = [
            'site' => [
                'name' => 'Yo! Symfony',
            ],
        ];

        $event = new FinishEvent($items, $siteAttributes);

        $this->assertTrue(is_array($event->getItems()));
        $this->assertTrue(is_array($event->getSiteAttributes()));

        $this->assertCount(1, $event->getItems());
        $this->assertCount(1, $event->getSiteAttributes());
    }
}
