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

use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\Core\Plugin\EventSubscriber;

/**
 * Base class for a command plugin.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class CommandPlugin implements CommandPluginInterface
{
    /**
     * @inheritDoc
     */
    public function getCommandDefinition()
    {
        throw new \RuntimeException('You must override the getCommandDefinition() method in the concrete command plugin class');
    }

    /**
     * @inheritDoc
     */
    public function executeCommand(IOInterface $io)
    {
        throw new \RuntimeException('You must override the getCommandDefinition() method in the concrete command plugin class');
    }

    /**
     * @inheritDoc
     */
    public function getMetas()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     *
     * Override this method in case you need a regular plugin behaviour.
     */
    public function initialize(EventSubscriber $subscriber)
    {
    }
}
