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

/**
 * Plugins collection.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PluginCollection implements \IteratorAggregate, \Countable
{
    private $plugins = [];

    /**
     * Returns an iterator over the plugins.
     * The key is the plugin's identifier and the
     * value is an instance of Yosymfony\Spress\Core\Plugin\PluginInterface.
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over plugins.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->plugins);
    }

    /**
     * Adds a new plugin in this collection.
     *
     * @param string                                       $name   The plugin identifier.
     * @param Yosymfony\Spress\Core\Plugin\PluginInterface $plugin The plugin.
     */
    public function add($name, PluginInterface $plugin)
    {
        if ($this->has($name) === false) {
            $this->set($name, $plugin);
        }
    }

    /**
     * Gets a plugin from the collection.
     *
     * @param string $name The plugin identifier.
     *
     * @return Yosymfony\Spress\Core\Plugin\PluginInterface
     *
     * @throws RuntimeException If the plugin is not defined.
     */
    public function get($name)
    {
        if ($this->has($name) === false) {
            throw new \RuntimeException(sprintf('Plugin "%s" not found.', $name));
        }

        return $this->plugins[$name];
    }

    /**
     * Gets the plugins in this collection.
     *
     * @return Yosymfony\Spress\Core\Plugin\PluginInterface[] A key-value array with the name of the plugin as key.
     */
    public function all()
    {
        return $this->plugins;
    }

    /**
     * Checks if a plugin exists in the collection.
     *
     * @param string $name The plugin identifier.
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->plugins[$name]);
    }

    /**
     * Sets a plugin.
     *
     * @param string                                       $name   The name of the plugin.
     * @param Yosymfony\Spress\Core\Plugin\PluginInterface $plugin The plugin.
     */
    public function set($name, PluginInterface $plugin)
    {
        $this->plugins[$name] = $plugin;
    }

    /**
     * Gets the number of plugins in this collection.
     *
     * @return int The number of plugins.
     */
    public function count()
    {
        return count($this->plugins);
    }

    /**
     * Removes a plugin from the collection.
     *
     * @param string $name The plugin identifier.
     */
    public function remove($name)
    {
        unset($this->plugins[$name]);
    }

    /**
     * Clears all plugins in this collection.
     */
    public function clear()
    {
        $this->plugins = [];
    }
}
