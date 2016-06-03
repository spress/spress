<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Support;

/**
 * A colection of elements.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Collection implements \IteratorAggregate, \Countable
{
    protected $elements = [];

    /**
     * Constructor.
     *
     * @param array $elements Elements.
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * Returns an iterator over the elements.
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over elements.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * Adds a new element in this collection.
     *
     * @param mixed $key     The key associated to the element.
     * @param mixed $element The element.
     */
    public function add($key, $element)
    {
        if ($this->has($key) === false) {
            $this->set($key, $element);
        }
    }

    /**
     * Gets a element from the collection.
     *
     * @param string $key The element identifier.
     *
     * @return mixed The element.
     *
     * @throws RuntimeException If the key is not defined.
     */
    public function get($key)
    {
        if ($this->has($key) === false) {
            throw new \RuntimeException(sprintf('Element with key: "%s" not found.', $key));
        }

        return $this->elements[$key];
    }

    /**
     * Gets the elements in this collection.
     *
     * @return array All elements in this collection.
     */
    public function all()
    {
        return $this->elements;
    }

    /**
     * Gets the keys registered in this collection.
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->elements);
    }

    /**
     * Checks if a elements exists in the collection.
     *
     * @param string $key The elements identifier or index.
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->elements);
    }

    /**
     * Sets an element.
     *
     * @param mixed $key     The key associated to the element.
     * @param mixed $element The element.
     */
    public function set($key, $element)
    {
        $this->elements[$key] = $element;
    }

    /**
     * Gets the number of elements in this collection.
     *
     * @return int The number of elements.
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * Removes a element from the collection.
     *
     * @param string $key The element identifier or index.
     */
    public function remove($key)
    {
        unset($this->elements[$key]);
    }

    /**
     * Clears all elements in this collection.
     */
    public function clear()
    {
        $this->elements = [];
    }
}
