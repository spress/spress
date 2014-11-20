<?php

/**
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core;

/**
 * A factory for create Twig instances
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 *
 * Usage:
 * <code>
 *  $twig = new TwigFactory();
 *  $twig->withAutoescape(false)
 *      ->withCache(false)          // or withCache('your/path')
 *      ->addLoaderFilesystem(array(
 *          'your/template/path',
 *          'namespace' => 'your/template/path2',
 *      ))
 *      ->addLoaderString()
 *      ->create();
 * </code>
 */
class TwigFactory
{
    private $loader = [];
    private $environmentOpt = [];

    public function __construct()
    {
        \Twig_Autoloader::register();
        $this->withDebug(false);
    }

    /**
     * Set cache path
     *
     * @param $value mixed Path to cache dir or false to disable cache
     *
     * @return TwigFactory Fluent interface
     */
    public function withCache($value)
    {
        $this->environmentOpt['cache'] = $value;

        return $this;
    }

    /**
     * Activate debug mode
     *
     * @param bool $value
     *
     * @return TwigFactory Fluent interface
     */
    public function withDebug($value)
    {
        $this->environmentOpt['debug'] = $value;

        return $this;
    }

    /**
     * With Strict variable set, Twig will throw an error if a
     * variable or attribute does not exist.
     *
     * @param bool $value
     *
     * @return TwigFactory Fluent interface
     */
    public function withStrictVariables($value)
    {
        $this->environmentOpt['strict_variables'] = $value;

        return $this;
    }

    /**
     * Set the autoescape strategy
     *
     * @param $value mixed css, url, html_attr, callback function or false to disable
     *
     * @return TwigFactory Fluent interface
     */
    public function withAutoescape($value)
    {
        $this->environmentOpt['autoescape'] = $value;

        return $this;
    }

    /**
     * Add String loader
     *
     * @return TwigFactory Fluent interface
     */
    public function addLoaderString()
    {
        $this->loader[] = new \Twig_Loader_String();

        return $this;
    }

    /**
     * Add Array loader
     *
     * @param array $values
     *
     * @return TwigFactory Fluent interface
     */
    public function addLoaderArray(array $values)
    {
        $this->loader[] = new \Twig_Loader_Array($values);

        return $this;
    }

    /**
     * Add Filesystem loader.
     * Usage:
     * <code>
     *  // Simple paths
     *  $factory->addLoaderFilesystem('your/path/to/templates');
     *
     *  // Multiple paths
     *  $factory->addLoaderFilesystem(array('your/path1/to/templates', 'your/path2/to/templates'));
     *
     *  // With namespaces
     *  $factory->addLoaderFilesystem(array(
     *      'namespace1' => 'your/path1/to/templates',
     *      'namespace2' => 'your/path2/to/templates',
     *  ));
     * </code>
     *
     * @return TwigFactory Fluent interface
     */
    public function addLoaderFilesystem($paths)
    {
        $loader = new \Twig_Loader_Filesystem();

        if (is_string($paths)) {
            $loader->addPath($paths);
        }

        if (is_array($paths)) {
            foreach ($paths as $namespace => $path) {
                if (is_string($namespace)) {
                    $loader->addPath($path, $namespace);
                } else {
                    $loader->addPath($path);
                }
            }
        }

        $this->loader[] = $loader;

        return $this;
    }

    /**
     * Create a Twig instance
     *
     * @return Twig_Environment
     */
    public function create()
    {
        $twigLoader = new \Twig_Loader_Chain($this->loader);
        $twig = new \Twig_Environment($twigLoader, $this->environmentOpt);

        if (true === $this->environmentOpt['debug']) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }

        return $twig;
    }
}
