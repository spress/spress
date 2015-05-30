<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Generator;

/**
 * Generator manager.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class GeneratorManager
{
    private $generators = [];

    /**
     * Adds a new generator.
     *
     * @param string                                                             $name      The generator name.
     * @param \Yosymfony\Spress\Core\ContentManager\Generator\GeneratorInterface $generator
     *
     * @throws RuntimeException If a previous generator exists with the same name.
     */
    public function addGenerator($name, GeneratorInterface $generator)
    {
        if ($this->hasGenerator($name) === true) {
            throw new \RuntimeException(sprintf('A previous generator exists with the same name: "%s".', $name));
        }

        $this->setGenerator($name, $generator);
    }

    /**
     * Sets a generator.
     *
     * @param string                                                             $name      The generator name.
     * @param \Yosymfony\Spress\Core\ContentManager\Generator\GeneratorInterface $generator
     */
    public function setGenerator($name, GeneratorInterface $generator)
    {
        $this->generators[$name] = $generator;
    }

    /**
     * Gets a generator.
     *
     * @param string $name The generator name.
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Generator\GeneratorInterface
     *
     * @throws RuntimeException If the generator is not defined.
     */
    public function getGenerator($name)
    {
        if ($this->hasGenerator($name) === false) {
            throw new \RuntimeException(sprintf('Generator not found: "%s".', $name));
        }

        return $this->generators[$name];
    }

    /**
     * Checks if a generator exists.
     *
     * @param string $name The generator name.
     *
     * @return bool
     */
    public function hasGenerator($name)
    {
        return isset($this->generators[$name]);
    }

    /**
     * Counts the generators registered.
     *
     * @return int
     */
    public function countGenerator()
    {
        return count($this->generators);
    }

    /**
     * Clears all generators registered.
     */
    public function clearGenerator()
    {
        $this->generators = [];
    }

    /**
     * Removes a generator.
     *
     * @param string $name The generator name.
     */
    public function removeGenerator($name)
    {
        unset($this->generators[$name]);
    }
}
