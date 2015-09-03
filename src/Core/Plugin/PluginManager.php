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

/**
 * Plugins manager.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PluginManager
{
    private $plugins = [];
    private $eventSubscriberPlugins = [];
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        $this->clearPlugin();
    }

    /**
     * Invokes initialize method for each plugin registered.
     */
    public function callInitialize()
    {
        foreach ($this->plugins as $plugin) {
            $subscriber = new EventSubscriber();
            $plugin->initialize($subscriber);

            $this->eventSubscriberPlugins[] = [$plugin, $subscriber];

            $this->addListeners($plugin, $subscriber);
        }
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

    /**
     * Adds a new plugin.
     *
     * @param string                                       $name   The plugin identifier.
     * @param Yosymfony\Spress\Core\Plugin\PluginInterface $plugin The plugin.
     */
    public function addPlugin($name, PluginInterface $plugin)
    {
        if ($this->hasPlugin($name) === false) {
            $this->setPlugin($name, $plugin);
        }
    }

    /**
     * Gets a plugin.
     *
     * @param string $name The plugin identifier.
     *
     * @return Yosymfony\Spress\Core\Plugin\PluginInterface
     *
     * @throws RuntimeException If the plugin is not defined.
     */
    public function getPlugin($name)
    {
        if ($this->hasPlugin($name) === false) {
            throw new \RuntimeException(sprintf('Plugin "%s" not found.', $name));
        }

        return $this->plugins[$name];
    }

    /**
     * Gets the plugins registered.
     *
     * @return Yosymfony\Spress\Core\Plugin\PluginInterface[] A key-value array with the name of the plugin as key.
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Checks if a plugin exists.
     *
     * @param string $name The plugin identifier.
     *
     * @return bool
     */
    public function hasPlugin($name)
    {
        return isset($this->plugins[$name]);
    }

    /**
     * Sets a plugin.
     *
     * @param string                                       $name   The name of the plugin.
     * @param Yosymfony\Spress\Core\Plugin\PluginInterface $plugin The plugin.
     */
    public function setPlugin($name, PluginInterface $plugin)
    {
        $this->plugins[$name] = $plugin;
    }

    /**
     * Counts the plugins registered.
     *
     * @return int
     */
    public function countPlugins()
    {
        return count($this->plugins);
    }

    /**
     * Clears all generators registered.
     */
    public function clearPlugin()
    {
        $this->plugins = [];
        $this->eventSubscriberPlugins = [];
    }

    /**
     * Removes a plugin.
     *
     * @param string $name The plugin identifier.
     */
    public function removePlugin($name)
    {
        unset($this->plugins[$name]);
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
