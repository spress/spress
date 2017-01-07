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

use Yosymfony\Spress\Core\Spress;

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

    /**
     * {@inheritdoc}
     */
    public function getSpress($siteDir = null)
    {
        $spress = new Spress();
        $spress['spress.config.default_filename'] = __DIR__.'/../../../app/config/config.yml';

        if (is_null($siteDir) === false) {
            $spress['spress.config.site_dir'] = $siteDir;
        }

        return $spress;
    }
}
