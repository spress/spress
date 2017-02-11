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

use Yosymfony\Spress\PackageManager\PackageNameVersion;

/**
 * Theme generator.
 *
 * @author Victor Puertas <vpuertas@gmail.com>
 */
class ThemeGenerator extends SiteGenerator
{
    /**
     * Scaffolds a new theme. In case of exception, the new-site directory
     * will be removed.
     *
     * @param string $path       Destination path
     * @param string $themeName  Theme name. Pattern <theme_name>:<theme_version>
     *                           can be used. "blank" is a special theme
     * @param string $repository Provide a custom repository to search for the
     *                           package, which will be used instead of packagist
     * @param bool   $force      Force to clear destination if exists and it's
     *                           not empty'
     * @param array  $options    Options: "prefer-source", "prefer-lock" or "repository"
     *
     * @throws LogicException If there is an attemp of create a non-blank template without the PackageManager
     */
    public function generate($path, $themeName, $force = false, array $options = [])
    {
        if ($themeName === self::BLANK_THEME) {
            parent::generate($path, $themeName, $force, $options);

            return;
        }

        $options = $this->getGenerateOptionsResolver()->resolve($options);

        $this->checkThemeName($themeName);
        $this->processPath($path, $force);
        $this->checkRequirements($themeName);

        $this->packageManager->createThemeProject(
            $path,
            $themeName,
            $options['repository'],
            $options['prefer-source']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getInitialPackagePairs()
    {
        return [new PackageNameVersion($this->spressInstallerPackage)];
    }

    /**
     * {@inheritdoc}
     */
    protected function getGenerateOptionsResolver()
    {
        $resolver = parent::createOptionsResolver();
        $resolver->setDefault('repository', null, 'string', false, true);

        return $resolver;
    }
}
