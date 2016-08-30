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
 * Plugin generator.
 *
 * @author Victor Puertas <vpuertas@gmail.com>
 */
class PluginGenerator extends Generator
{
    protected $name;
    protected $targetDir;
    protected $namespace;
    protected $commandName;
    protected $commandDescription;
    protected $commandHelp;
    protected $author;
    protected $email;
    protected $description;
    protected $license;
    protected $dirctoryName;
    protected $licenses = [
        'MIT' => 'plugin/MIT.twig',
        'APACHE-2.0' => 'plugin/Apache-2.0.twig',
        'BSD-2-CLAUSE' => 'plugin/BSD-2-Clause.twig',
        'GPL-3.0' => 'plugin/GPL-3.0.twig',
        'LGPL-3.0' => 'plugin/LGPL-3.0.twig',
    ];

    /**
     * Constructor.
     *
     * @param string $targetDir The target dir
     * @param string $name      The name of the plugin. Could you follow the pattern "vendor/class-name"
     */
    public function __construct($targetDir, $name)
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

        $this->name = $name;
        $this->namespace = '';
        $this->targetDir = $targetDir;
        $this->license = 'MIT';
    }

    /**
     * Sets the namespace of the plugin.
     *
     * @param string $value
     */
    public function setNamespace($value)
    {
        $this->namespace = $value;
    }

    /**
     * Sets the command's data in case of command plugin.
     *
     * @param string $name        The name of the command
     * @param string $description The description of the command
     */
    public function setCommandData($name, $description = '', $help = '')
    {
        $this->commandName = $name;
        $this->commandDescription = $description;
        $this->commandHelp = $help;
    }

    /**
     * Sets the author of the plugin.
     *
     * @param string $name  The name of the author
     * @param string $email The Email of the author
     */
    public function setAuthor($name, $email = '')
    {
        $this->author = $name;
        $this->email = $email;
    }

    /**
     * Sets the description of the plugin.
     *
     * @param string $value
     */
    public function setDescription($value)
    {
        $this->description = $value;
    }

    /**
     * Sets the license of the plugin. MIT by default.
     *
     * @param string $value
     */
    public function setLicense($name)
    {
        $this->license = $name;
    }

    /**
     * Gets the plugin directory.
     *
     * @return string
     */
    public function getPluginDirName()
    {
        return $this->dirctoryName;
    }

    /**
     * Generates a plugin.
     *
     * @return array
     */
    public function generate()
    {
        $this->dirctoryName = $this->getPluginDir($this->name);

        $pluginDir = $this->targetDir.'/'.$this->dirctoryName;

        if (file_exists($pluginDir)) {
            throw new \RuntimeException(sprintf('Unable to generate the plugin as the plugin directory "%s" exists.', $pluginDir));
        }

        $model = [
            'name' => $this->name,
            'classname' => $this->getClassname($this->name),
            'namespace' => $this->namespace,
            'namespace_psr4' => $this->getNamespacePsr4($this->namespace),
            'author' => $this->author,
            'email' => $this->email,
            'description' => $this->description,
            'license' => $this->license,
        ];

        $this->cleanFilesAffected();

        $pluginTemplateFile = 'plugin/plugin.php.twig';

        if (empty($this->commandName) === false) {
            $pluginTemplateFile = 'plugin/commandPlugin.php.twig';

            $model['command_name'] = $this->commandName;
            $model['command_description'] = $this->commandDescription;
            $model['command_help'] = $this->commandHelp;
        }

        $this->renderFile($pluginTemplateFile, $pluginDir.'/'.$this->getPluginFilename($this->name), $model);
        $this->renderFile('plugin/composer.json.twig', $pluginDir.'/composer.json', $model);

        $licenseFile = $this->getLicenseFile($this->license);

        if ($licenseFile) {
            $model = [
                'author' => $this->author,
            ];

            $this->renderFile($licenseFile, $pluginDir.'/LICENSE', $model);
        }

        return $this->getFilesAffected();
    }

    /**
     * Gets the classname.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getClassname($name)
    {
        $result = implode(' ', explode('/', $name));
        $result = implode(' ', explode('-', $result));

        $result = ucwords($result);

        // replace non letter or digits by empty string
        $result = preg_replace('/[^\\pL\d]+/u', '', $result);

        // trim
        $result = trim($result, '-');

        // transliterate
        $result = iconv('UTF-8', 'US-ASCII//TRANSLIT', $result);

        // remove unwanted characters
        $result = preg_replace('/[^-\w]+/', '', $result);

        return $result;
    }

    /**
     * Gets the plugin directory.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPluginDir($name)
    {
        return $this->getClassname($name);
    }

    /**
     * Gets the plugin filename.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPluginFilename($name)
    {
        return sprintf('%s.php', $this->getClassname($name));
    }

    /**
     * Gets the namepspace for PSR-4.
     *
     * @param string $namespace
     *
     * @return string
     */
    protected function getNamespacePsr4($namespace)
    {
        return str_replace('\\', '\\\\', $namespace).'\\\\';
    }

    /**
     * Gets the license filename.
     *
     * @param string $licenseName
     *
     * @return string Filename or empty-string if not exists
     */
    protected function getLicenseFile($licenseName)
    {
        return isset($this->licenses[strtoupper($licenseName)]) ? $this->licenses[strtoupper($licenseName)] : '';
    }
}
