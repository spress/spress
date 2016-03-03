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
 * Collection-items collection.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ColectionItemCollection implements \IteratorAggregate, \Countable
{
    private $collections = [];

    /**
     * Returns an iterator over the collection-items.
     * The key is the collection's name and the
     * value is an instance of Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface.
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over collection-items.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->collections);
    }

    /**
     * Adds a new collection-item in this collection.
     *
     * @param Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface The collection-item.
     */
    public function add(CollectionInterface $collection)
    {
        if ($this->has($collection->getName()) === true) {
            throw new \RuntimeException(sprintf(
                'A previous collection exists with the same name: "%s".',
                $collection->getName()));
        }

        $this->set($collection);
    }

    /**
     * Gets a collection-item from the collection.
     *
     * @param string $name The collection's name.
     *
     * @return Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface
     *
     * @throws RuntimeException If the collection-item is not defined.
     */
    public function get($name)
    {
        if (false === $this->has($name)) {
            throw new \RuntimeException(sprintf('Collection: "%s" not found.', $name));
        }

        return $this->collections[$name];
    }

    /**
     * Gets the collection-items in this collection.
     *
     * @return Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface[] A key-value array with the name of the collection as key.
     */
    public function all()
    {
        return $this->collections;
    }

    /**
     * Checks if a collection-item exists in the collection.
     *
     * @param string $name The collection's name.
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->collections[$name]);
    }

    /**
     * Sets a collection-item.
     *
     * @param Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface $collection
     */
    public function set(CollectionInterface $collection)
    {
        $this->collections[$collection->getName()] = $collection;
    }

    /**
     * Gets the number of collection-items in this collection.
     *
     * @return int The number of collection-item.
     */
    public function count()
    {
        return count($this->collections);
    }

    /**
     * Removes a collection-item from the collection.
     *
     * @param string $name The collection's name.
     */
    public function remove($name)
    {
        unset($this->collections[$name]);
    }

    /**
     * Clears all collection-items in this collection.
     */
    public function clear()
    {
        $this->collections = [];
    }
}
