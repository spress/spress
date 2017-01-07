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
use Yosymfony\Spress\Plugin\Environment\CommandEnvironmentInterface;

/**
 * Command plugin interface.
 *
 * @api
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface CommandPluginInterface extends PluginInterface
{
    /**
     * Gets the command's definition.
     *
     * @return Yosymfony\Spress\Plugin\CommandDefinition Definition of the command
     */
    public function getCommandDefinition();

    /**
     * The body of the command.
     *
     * @param Yosymfony\Spress\Core\IO\IOInterface $io        Input/output interface
     * @param array                                $arguments Arguments passed to the command
     * @param array                                $options   Options passed to the command
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    public function executeCommand(IOInterface $io, array $arguments, array $options);

    /**
     * Sets the command environment.
     *
     * @param Yosymfony\Spress\Plugin\Environment\CommandEnvironmentInterface $environment The environment
     */
    public function setCommandEnvironment(CommandEnvironmentInterface $environment);

    /**
     * Gets the command environment.
     *
     * @return Yosymfony\Spress\Plugin\Environment\CommandEnvironmentInterface
     */
    public function getCommandEnvironment();
}
