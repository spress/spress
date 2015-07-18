<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Command;

use Yosymfony\Spress\Core\Spress;
use Yosymfony\Spress\Core\IO\IOInterface;

/**
 * Spress core wrapper for CLI.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SpressCLI extends Spress
{
    /**
     * Constructor.
     *
     * @param IOInterface $io
     */
    public function __construct(IOInterface $io)
    {
        parent::__construct();

        $this['spress.io'] = $io;
    }

    /**
     * Gets the template path.
     *
     * @return string
     */
    public function getTemplatesPath()
    {
        $spressPath = __DIR__.'/../../';

        if (file_exists($spressPath.'app/templates/')) {
            return $spressPath.'app/templates';
        }

        return $spressPath.'../spress-templates';
    }
}
