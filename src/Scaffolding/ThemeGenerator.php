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
     * Scaffold a new theme. In case of exception, the new-site directory
     * will be removed.
     *
     * @param string $path         Destination path
     * @param string $themeName    Theme name. Pattern <theme_name>:<theme_version>
     *                             can be used. "blank" is a special theme
     * @param string $repository   Provide a custom repository to search for the
     *                             package, which will be used instead of packagist
     * @param bool   $force        Force to clear destination if exists and it's
     *                             not empty'
     * @param bool   $preferSource Grab the theme from source when available
     *
     * @throws LogicException If there is an attemp of create a non-blank template without the PackageManager
     */
    public function generate($path, $themeName, $repository = null, $force = false, $preferSource = false)
    {
        if ($themeName === self::BLANK_THEME) {
            parent::generate($path, $themeName, $force);

            return;
        }

        $this->checkThemeName($themeName);
        $this->processPath($path, $force);
        $this->checkRequirements($themeName);

        $this->packageManager->createThemeProject($path, $themeName, $repository, $preferSource);
    }
}
