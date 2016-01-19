<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Collection;

/**
 * Collection.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Collection implements CollectionInterface
{
    private $name;
    private $path;
    private $attributes;

    /**
     * Constructor.
     *
     * @param string $name       The collection's name
     * @param string $path       The collection's relative path
     * @param array  $attributes
     */
    public function __construct($name, $path, array $attributes = [])
    {
        if (is_string($name) === false) {
            throw new \RuntimeException('Expected string as the name of the collection.');
        }

        if (is_string($path) === false) {
            throw new \RuntimeException('Expected string as the path of the collection.');
        }

        $this->name = $name;
        $this->path = $path;
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }
}
