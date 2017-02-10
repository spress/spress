<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Plugin;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Yosymfony\Spress\Core\Support\AttributesResolver;
use Yosymfony\Spress\Core\Support\Collection;

/**
 * Plugin manager builder.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PluginManagerBuilder
{
    protected $path;
    protected $excludeDirs;
    protected $resolver;
    protected $eventDispatcher;
    protected $composerFilename;

    /**
     * Constructor.
     *
     * @param string          $path            Path to plugin folder
     * @param EventDispatcher $eventDispatcher
     * @param array           $excludeDirs     List of directories excluded of the scan
     *                                         for discovering class. Tests directories are a good example
     */
    public function __construct(
        $path,
        EventDispatcher $eventDispatcher,
        $excludeDirs = [])
    {
        $this->path = $path;
        $this->excludeDirs = $excludeDirs;
        $this->eventDispatcher = $eventDispatcher;
        $this->resolver = $this->getResolver();
        $this->setComposerFilename('composer.json');
    }

    /**
     * Sets the composer filename. "composer.json" by default.
     *
     * @param string $name The filename
     */
    public function setComposerFilename($name)
    {
        $this->composerFilename = $name;
    }

    /**
     * Builds the PluginManager with the plugins of a site.
     *
     * Each plugin is added to PluginManager using "name"
     * meta or its classname if that meta does not exists.
     *
     * @return PluginManager PluginManager filled with the valid plugins
     */
    public function build()
    {
        $pm = new PluginManager($this->eventDispatcher);
        $pluginCollection = $pm->getPluginCollection();

        if (file_exists($this->path) === false) {
            return $pm;
        }

        $classnamesFromComposerFile = [];

        $finder = $this->buildFinder();

        foreach ($finder as $file) {
            if ($file->getFilename() === $this->composerFilename) {
                $classes = $this->getClassnamesFromComposerFile($file);
                $classnamesFromComposerFile = array_merge($classnamesFromComposerFile, $classes);

                continue;
            }

            $classname = $this->getClassnameFromPHPFilename($file);

            include_once $file->getRealPath();

            if ($this->hasImplementedPluginInterface($classname) === false) {
                continue;
            }

            $this->addPluginToCollection($classname, $pluginCollection);
        }

        foreach ($classnamesFromComposerFile as $classname) {
            if ($this->hasImplementedPluginInterface($classname) === true) {
                $this->addPluginToCollection($classname, $pluginCollection);
            }
        }

        return $pm;
    }

    protected function isValidPluginFolder($path)
    {
        return empty($path) === true || file_exists($path) === false;
    }

    /**
     * Adds a new valid list of plugins (they must implements PluginInterface) to a collections
     * of plugins. It performs a new operation for each valid plugin.
     *
     * @param string     $classname
     * @param Collection $pluginCollection
     */
    protected function addPluginToCollection($classname, Collection $pluginCollection)
    {
        $plugin = new $classname();
        $metas = $this->getPluginMetas($plugin);
        $pluginCollection->add($metas['name'], $plugin);
    }

    /**
     * Extracts the class names from the filename.
     *
     * @param SplFileInfo $file A PHP file
     *
     * @return string Class name
     */
    protected function getClassnameFromPHPFilename(SplFileInfo $file)
    {
        return $file->getBasename('.php');
    }

    /**
     * Extracts the class names from a composer.json file
     * A composer.json file could defines several classes in spress_class
     * property from extra section.
     *
     * @param SplFileInfo $file A composer.json file
     *
     * @return array List of class names
     */
    protected function getClassnamesFromComposerFile(SplFileInfo $file)
    {
        $composerData = $this->readComposerFile($file);

        if (isset($composerData['extra']['spress_class']) === false) {
            return [];
        }

        if (
            is_string($composerData['extra']['spress_class']) === false
            && is_array($composerData['extra']['spress_class']) === false
        ) {
            return [];
        }

        return (array) $composerData['extra']['spress_class'];
    }

    /**
     * Gets metas of a plugin.
     *
     * @param string          $filename The plugin filename
     * @param PluginInterface $plugin   The plugin
     *
     * @return array
     *
     * @throws RuntimeException If bad metas
     */
    protected function getPluginMetas(PluginInterface $plugin)
    {
        $metas = $plugin->getMetas();

        if (is_array($metas) === false) {
            $classname = get_class($plugin);

            throw new \RuntimeException(sprintf('Expected an array at method "getMetas" of the plugin: "%s".', $classname));
        }

        $metas = $this->resolver->resolve($metas);

        if (empty($metas['name']) === true) {
            $metas['name'] = get_class($plugin);
        }

        return $metas;
    }

    /**
     * Checks if the class implements the PluginInterface.
     *
     * @param string $name Class's name
     *
     * @return bool
     */
    protected function hasImplementedPluginInterface($name)
    {
        $result = false;

        if (class_exists($name)) {
            $implements = class_implements($name);

            if (isset($implements['Yosymfony\\Spress\\Core\\Plugin\\PluginInterface'])) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Reads a "composer.json" file.
     *
     * @param SplFileInfo $file The file
     *
     * @return array The parsed JSON filename
     */
    protected function readComposerFile(SplFileInfo $file)
    {
        $json = $file->getContents();
        $data = json_decode($json, true);

        return $data;
    }

    /**
     * Gets the attribute resolver.
     *
     * @return AttributesResolver
     */
    protected function getResolver()
    {
        $resolver = new AttributesResolver();
        $resolver->setDefault('name', '', 'string')
            ->setDefault('description', '', 'string')
            ->setDefault('author', '', 'string')
            ->setDefault('license', '', 'string');

        return $resolver;
    }

    /**
     * Returns a Finder set up for finding both composer.json and php files.
     *
     * @return Finder A Symfony Finder instance
     */
    protected function buildFinder()
    {
        $finder = new Finder();
        $finder->files()
            ->name('/(\.php|composer\.json)$/')
            ->in($this->path)
            ->exclude($this->excludeDirs);

        return $finder;
    }
}
