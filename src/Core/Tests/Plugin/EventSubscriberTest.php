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

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\Plugin\EventSubscriber;

class EventSubscriberTest extends TestCase
{
    public function testGetEventListeners()
    {
        $eventSubscriber = new EventSubscriber();
        $eventSubscriber->addEventListener('spress.test', function () {
            $a = 'event logic';
        });

        $listeners = $eventSubscriber->getEventListeners();

        $this->assertTrue(is_array($listeners));
        $this->assertCount(1, $listeners);
        $this->assertTrue(array_key_exists('spress.test', $listeners));
    }

    public function testGetEventListenersEmpty()
    {
        $eventSubscriber = new EventSubscriber();

        $listeners = $eventSubscriber->getEventListeners();

        $this->assertTrue(is_array($listeners));
        $this->assertCount(0, $listeners);
    }
}
