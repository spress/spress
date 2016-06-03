<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Plugin;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Yosymfony\Spress\Core\Support\Collection;

/**
 * Plugins manager.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PluginManager
{
    private $pluginCollection;
    private $eventSubscriberPlugins = [];
    private $eventDispatcher;

    /**
     * Constructor.
     *
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->pluginCollection = new Collection();
    }

    /**
     * Invokes initialize method for each plugin registered.
     */
    public function callInitialize()
    {
        foreach ($this->pluginCollection as $plugin) {
            $subscriber = new EventSubscriber();
            $plugin->initialize($subscriber);

            $this->eventSubscriberPlugins[] = [$plugin, $subscriber];

            $this->addListeners($plugin, $subscriber);
        }
    }

    /**
     * Gets the plugin collection.
     *
     * @return Yosymfony\Spress\Core\Support\Collection The plugin collection.
     */
    public function getPluginCollection()
    {
        return $this->pluginCollection;
    }

    /**
     * Releases resources like event listeners.
     */
    public function tearDown()
    {
        foreach ($this->eventSubscriberPlugins as list($plugin, $eventSubscriber)) {
            $this->removeListeners($plugin, $eventSubscriber);
        }
    }

    private function addListeners(PluginInterface $plugin, EventSubscriber $subscriber)
    {
        foreach ($subscriber->getEventListeners() as $eventName => $listener) {
            if (true === is_string($listener)) {
                $this->eventDispatcher->addListener($eventName, [$plugin, $listener]);
            } else {
                $this->eventDispatcher->addListener($eventName, $listener);
            }
        }
    }

    private function removeListeners(PluginInterface $plugin, EventSubscriber $subscriber)
    {
        foreach ($subscriber->getEventListeners() as $eventName => $listener) {
            if (true === is_string($listener)) {
                $this->eventDispatcher->removeListener($eventName, [$plugin, $listener]);
            } else {
                $this->eventDispatcher->removeListener($eventName, $listener);
            }
        }
    }
}
