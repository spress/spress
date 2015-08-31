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
use Yosymfony\Spress\Core\Plugin\PluginInterface;

/**
 * Command plugin interface.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface CommandPluginInterface extends PluginInterface
{
    /**
     * Gets the command's definition.
     *
     * @return \Yosymfony\Spress\Plugin\CommandDefinition Definition of the command.
     */
    public function getCommandDefinition();

    /**
     * Executes the current command.
     *
     * @param \Yosymfony\Spress\Core\IO\IOInterface $io Input/output interface.
     *
     * @return null|int null or 0 if everything went fine, or an error code.
     */
    public function executeCommand(IOInterface $io);
}
