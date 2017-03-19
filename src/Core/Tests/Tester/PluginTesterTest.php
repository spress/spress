<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\Plugin\EventSubscriber;
use Yosymfony\Spress\Core\Tester\PluginTester;

class PluginTesterTest extends TestCase
{
    public function testMetas()
    {
        $plugin = new PluginTester('acme');

        $this->assertTrue(is_array($plugin->getMetas()));
        $this->assertArrayHasKey('name', $plugin->getMetas());
        $this->assertEquals('acme', $plugin->getMetas()['name']);

        $plugin->setMetas([
            'name' => 'acme2',
        ]);

        $this->assertArrayHasKey('name', $plugin->getMetas());
        $this->assertEquals('acme2', $plugin->getMetas()['name']);
    }

    public function testListeners()
    {
        $plugin = new PluginTester('acme');
        $eventSubscriber = new EventSubscriber();

        $plugin->setListenerToStartEvent(function ($event) {
        });
        $plugin->setListenerToBeforeConvertEvent(function ($event) {
        });
        $plugin->setListenerToAfterConvertEvent(function ($event) {
        });
        $plugin->setListenerToBeforeRenderBlocksEvent(function ($event) {
        });
        $plugin->setListenerToAfterRenderBlocksEvent(function ($event) {
        });
        $plugin->setListenerToBeforeRenderPageEvent(function ($event) {
        });
        $plugin->setListenerToAfterRenderPageEvent(function ($event) {
        });
        $plugin->setListenerToFinishEvent(function ($event) {
        });

        $plugin->initialize($eventSubscriber);

        $this->assertCount(8, $eventSubscriber->getEventListeners());

        $this->assertArrayHasKey('spress.start', $eventSubscriber->getEventListeners());
        $this->assertArrayHasKey('spress.before_convert', $eventSubscriber->getEventListeners());
        $this->assertArrayHasKey('spress.after_convert', $eventSubscriber->getEventListeners());
        $this->assertArrayHasKey('spress.before_render_blocks', $eventSubscriber->getEventListeners());
        $this->assertArrayHasKey('spress.after_render_blocks', $eventSubscriber->getEventListeners());
        $this->assertArrayHasKey('spress.before_render_page', $eventSubscriber->getEventListeners());
        $this->assertArrayHasKey('spress.after_render_page', $eventSubscriber->getEventListeners());
        $this->assertArrayHasKey('spress.finish', $eventSubscriber->getEventListeners());
    }
}
