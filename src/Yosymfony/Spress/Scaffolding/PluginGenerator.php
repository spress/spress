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
 * Plugin generator
 *
 * @author Victor Puertas <vpuertas@gmail.com>
 */
class PluginGenerator extends Generator
{
    protected $dirctoryName;
    protected $licenses = [
        'MIT'           => 'plugin/MIT.twig',
        'APACHE-2.0'    => 'plugin/Apache-2.0.twig',
        'BSD-2-CLAUSE'  => 'plugin/BSD-2-Clause.twig',
        'GPL-3.0'       => 'plugin/GPL-3.0.twig',
        'LGPL-3.0'      => 'plugin/LGPL-3.0.twig',
    ];

    /**
     * Generate a plugin
     *
     * @param $targetDir string
     * @param $name string
     * @param $namespace string
     * @param $author string
     * @param $email string
     * @param $description string
     * @param $license string
     *
     * @return array
     */
    public function generate($targetDir, $name, $namespace = '', $author = '', $email = '', $description = '', $license = 'MIT')
    {
        if (0 === strlen(trim($name))) {
            throw new \RuntimeException('Unable to generate the plugin as the name is empty.');
        }

        if (file_exists($targetDir)) {
            if (false === is_dir($targetDir)) {
                throw new \RuntimeException(sprintf('Unable to generate the plugin as the target directory "%s" exists but is a file.', $targetDir));
            }

            if (false === is_writable($targetDir)) {
                throw new \RuntimeException(sprintf('Unable to generate the plugin as the target directory "%s" is not writable.', $targetDir));
            }
        }

        $this->dirctoryName = $this->getPluginDir($name);

        $pluginDir = $targetDir.'/'.$this->dirctoryName;

        if (file_exists($pluginDir)) {
            throw new \RuntimeException(sprintf('Unable to generate the plugin as the plugin directory "%s" exists.', $pluginDir));
        }

        $model = [
            'name'              => $name,
            'classname'         => $this->getClassname($name),
            'namespace'         => $namespace,
            'namespace_psr4'    => $this->getNamespacePsr4($namespace),
            'author'            => $author,
            'email'             => $email,
            'description'       => $description,
            'license'           => $license,
        ];

        $this->cleanFilesAffected();

        $this->renderFile('plugin/plugin.php.twig', $pluginDir.'/'.$this->getPluginFilename($name), $model);
        $this->renderFile('plugin/composer.json.twig', $pluginDir.'/composer.json', $model);

        $licenseFile = $this->getLicenseFile($license);

        if ($licenseFile) {
            $model = [
                'author' => $author
            ];

            $this->renderFile($licenseFile, $pluginDir.'/LICENSE', $model);
        }

        return $this->getFilesAffected();
    }

    /**
     * Get the directory of the plugin
     *
     * @return string
     */
    public function getPluginDirName()
    {
        return $this->dirctoryName;
    }

    protected function getClassname($name)
    {
        // replace non letter or digits by empty string
        $result = preg_replace('/[^\\pL\d]+/u', '', $name);

        // trim
        $result = trim($result, '-');

        // transliterate
        $result = iconv('UTF-8', 'US-ASCII//TRANSLIT', $result);

        // lowercase
        $result = strtolower($result);

        // remove unwanted characters
        $result = preg_replace('/[^-\w]+/', '', $result);

        return ucfirst($result);
    }

    protected function getPluginDir($name)
    {
        return $this->getClassname($name);
    }

    protected function getPluginFilename($name)
    {
        return sprintf('%s.php', $this->getClassname($name));
    }

    protected function getNamespacePsr4($namespace)
    {
        return str_replace('\\', '\\\\', $namespace).'\\\\';
    }

    protected function getLicenseFile($licenseName)
    {
        return isset($this->licenses[strtoupper($licenseName)]) ? $this->licenses[strtoupper($licenseName)] : '';
    }
}
