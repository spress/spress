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
 * Plugin interface.
 *
 * @api
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface PluginInterface
{
    /**
     * Metas of a plugin.
     *
     * Standard metas:
     *   - name: (string) The name of plugin.
     *   - description: (string) A short description of the plugin.
     *   - author: (string) The author of the plugin.
     *   - license: (string) The license of the plugin.
     *
     * e.g:
     *   return [
     *     'name' => 'Data loader',
     *     'description' => 'A simple dataloader with support for JSON and YAML.',
     *     'author' => 'Victor Puertas',
     *     'license' => 'MIT',
     *   ];
     *
     * @return array
     */
    public function getMetas();

    /**
     * Initialize plugin: subscribe to events and registers others extension
     * like datasources, generator, converter, renderizers or Twig extension.
     *
     * @param \Yosymfony\Spress\Core\Plugin\EventSubscriber $subscriber
     */
    public function initialize(EventSubscriber $subscriber);
}
