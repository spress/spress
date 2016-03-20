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
 * The default command environment implementation.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class DefaultCommandEnvironment implements CommandEnvironmentInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasCommand($name)
    {
        throw new \RuntimeException('The default command environment does not has support for "hasCommand" method.');
    }

    /**
     * {@inheritdoc}
     */
    public function runCommand($commandName, array $arguments)
    {
        throw new \RuntimeException('The default command environment does not has support for "runCommand" method.');
    }
}
