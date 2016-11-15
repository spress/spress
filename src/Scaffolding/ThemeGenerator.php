<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Scaffolding;

/**
 * Theme generator.
 *
 * @author Victor Puertas <vpuertas@gmail.com>
 */
class ThemeGenerator extends SiteGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate($path, $themeName, $force = false)
    {
        if ($themeName === self::BLANK_THEME) {
            parent::generate($path, $themeName, $force);

            return;
        }

        $this->checkRequirements($themeName);
        $this->packageManager->createProject($path, $themeName);
    }
}
