<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tester;

use Yosymfony\Spress\Core\Plugin\EventSubscriber;
use Yosymfony\Spress\Core\Plugin\PluginInterface;

/**
 * Plugin tester.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PluginTester implements PluginInterface
{
    protected $metas;
    protected $eventListeners;

    /**
     * Constructor.
     *
     * @param string $name The name of the plugin.
     */
    public function __construct($name)
    {
        $this->eventListeners = [];
        $this->metas = ['name' => $name];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetas()
    {
        return $this->metas;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(EventSubscriber $subscriber)
    {
        foreach ($this->eventListeners as $event => $listener) {
            $subscriber->addEventListener($event, $listener);
        }
    }

    /**
     * Set the metadata of the plugin.
     *
     * @param array $metas
     */
    public function setMetas(array $metas)
    {
        $this->metas = $metas;
    }

    /**
     * Set the listener to "spress.start" event.
     *
     * @param callable $listener
     */
    public function setListenerToStartEvent(callable $listener)
    {
        $this->eventListeners['spress.start'] = $listener;
    }

    /**
     * Set the listener to "spress.before_convert" event.
     *
     * @param callable $listener
     */
    public function setListenerToBeforeConvertEvent(callable $listener)
    {
        $this->eventListeners['spress.before_convert'] = $listener;
    }

    /**
     * Set the listener to "spress.after_convert" event.
     *
     * @param callable $listener
     */
    public function setListenerToAfterConvertEvent(callable $listener)
    {
        $this->eventListeners['spress.after_convert'] = $listener;
    }

    /**
     * Set the listener to "spress.before_render_blocks" event.
     *
     * @param callable $listener
     */
    public function setListenerToBeforeRenderBlocksEvent(callable $listener)
    {
        $this->eventListeners['spress.before_render_blocks'] = $listener;
    }

    /**
     * Set the listener to "spress.after_render_blocks" event.
     *
     * @param callable $listener
     */
    public function setListenerToAfterRenderBlocksEvent(callable $listener)
    {
        $this->eventListeners['spress.after_render_blocks'] = $listener;
    }

    /**
     * Set the listener to "spress.before_render_page" event.
     *
     * @param callable $listener
     */
    public function setListenerToBeforeRenderPageEvent(callable $listener)
    {
        $this->eventListeners['spress.before_render_page'] = $listener;
    }

    /**
     * Set the listener to "spress.after_render_page" event.
     *
     * @param callable $listener
     */
    public function setListenerToAfterRenderPageEvent(callable $listener)
    {
        $this->eventListeners['spress.after_render_page'] = $listener;
    }

    /**
     * Set the listener to "spress.finish" event.
     *
     * @param callable $listener
     */
    public function setListenerToFinishEvent(callable $listener)
    {
        $this->eventListeners['spress.finish'] = $listener;
    }
}
