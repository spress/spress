<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Plugin;

use Yosymfony\Spress\Core\Plugin\PluginInterface;

/**
 * Basic implementation of PluginInterface
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Plugin implements PluginInterface
{
    /**
     * {@inheritDoc}
     */
    public function getMetas()
    {
    }
    
    /**
     * {@inheritDoc}
     */
    public function initialize(EventSubscriber $subscriber)
    {
    }
}
