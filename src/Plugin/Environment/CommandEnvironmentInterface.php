<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Plugin\Environment;

/**
 * Command environment interface.
 *
 * @api
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface CommandEnvironmentInterface
{
    /**
     * Returns true if the command exists, false otherwise.
     *
     * This method must be used inside of executeCommand method implementation.
     *
     * @param string $name The name of the command. e.g: "site:build"
     *
     * @return bool
     */
    public function hasCommand($name);

    /**
     * Runs a command.
     *
     * @param string $commandName The name of the command. e.g: "site:build"
     * @param array  $arguments   The arguments
     *
     * @return int The command exit code
     *
     * @throws Yosymfony\Spress\Plugin\Environment\CommandNotFoundException When command name is incorrect
     * @throws Exception
     */
    public function runCommand($commandName, array $arguments);

    /**
     * Returns an Spress instance.
     *
     * @param string $siteDir The site directory. Null means the current dir
     *
     * @return Spress A Spress instance
     */
    public function getSpress($siteDir = null);
}
