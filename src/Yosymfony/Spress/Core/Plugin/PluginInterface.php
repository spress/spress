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

use Yosymfony\Spress\Plugin\EventSubscriber;

/**
 * Plugin interface
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface PluginInterface
{
    /**
     * Metas of a plugin
     * 
     * Standard metas:
     *   - name: name of plugin
     *   - author: author of plugin
     * 
     * @return null or array
     */
    public function getMetas();
    
    /**
     * Initialize plugin: subscribe to events and registers others extension
     * like converters o Twig extension
     * 
     * @param EventSubscriber $subscriber
     */
    public function initialize(EventSubscriber $subscriber);
}
