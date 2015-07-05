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
use Dflydev\EmbeddedComposer\Core\EmbeddedComposerInterface;
use Yosymfony\Spress\Core\Support\AttributesResolver;

/**
 * Plugins manager builder.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PluginManagerBuilder
{
    protected $finder;
    protected $resolver;
    protected $eventDispatcher;
    protected $embeddedComposer;
    protected $composerFilename;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Finder\Finder                         $finder           Finder to locate plugins.
     * @param \Dflydev\EmbeddedComposer\Core\EmbeddedComposerInterface $embeddedComposer
     * @param \Symfony\Component\EventDispatcher\EventDispatcher       $eventDispatcher
     */
    public function __construct(
        Finder $finder,
        EmbeddedComposerInterface $embeddedComposer,
        EventDispatcher $eventDispatcher)
    {
        $this->finder = $finder;
        $this->eventDispatcher = $eventDispatcher;
        $this->embeddedComposer = $embeddedComposer;
        $this->resolver = $this->getResolver();
        $this->setComposerFilename('composer.json');
    }

    /**
     * Sets the composer filename. "composer.json" by default.
     *
     * @param string $name The filename.
     */
    public function setComposerFilename($name)
    {
        $this->composerFilename = $name;
    }

    /**
     * Builds the PluginManager with the plugins of a site.
     *
     * @return \Yosymfony\Spress\Core\Plugin\PluginManager
     */
    public function build()
    {
        $pm = new PluginManager($this->eventDispatcher);

        $this->embeddedComposer->processAdditionalAutoloads();

        foreach ($this->finder as $file) {
            $classname = $this->getClassname($file);

            if (empty($classname) === true) {
                continue;
            }

            if (stripos($classname, '\\', 1) === false) {
                include_once $file->getRealPath();
            }

            if ($this->isValidClassName($classname) === false) {
                continue;
            }

            $plugin = new $classname();
            $metas = $this->getPluginMetas($file->getRelativePathname(), $plugin);

            $pm->addPlugin($metas['name'], $plugin);
        }

        return $pm;
    }

    /**
     * Gets the class name.
     *
     * @param \Symfony\Component\Finder\SplFileInfo $file
     *
     * @return string
     */
    protected function getClassname(SplFileInfo $file)
    {
        if ($file->getExtension() === 'php') {
            return $file->getBasename('.php');
        }

        if ($file->getFilename() === $this->composerFilename) {
            $composerData = $this->readComposerFile($file);

            if (isset($this->data['extra']['spress_class'])) {
                return $this->data['extra']['spress_class'];
            }
        }

        return '';
    }

    /**
     * Gets metas of a plugin.
     *
     * @param string                                        $filename The plugin filename.
     * @param \Yosymfony\Spress\Core\Plugin\PluginInterface $plugin   The plugin.
     *
     * @return array
     */
    protected function getPluginMetas($filename, PluginInterface $plugin)
    {
        $metas = $plugin->getMetas();

        if (is_array($metas) === false) {
            throw new \RuntimeException(sprintf('Expected an array at method "getMetas" of the plugin: "%s"', $filename));
        }

        $metas = $this->resolver->resolve($metas);

        if (empty($metas['name']) === true) {
            $metas['name'] = get_class($plugin);
        }

        return $metas;
    }

    /**
     * Checks if the class name is valid.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function isValidClassName($name)
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
     * @param \Symfony\Component\Finder\SplFileInfo $file
     *
     * @return Array The parsed json filename.
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
     * @return \Yosymfony\Spress\Core\Support\AttributesResolver\AttributesResolver
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
}
