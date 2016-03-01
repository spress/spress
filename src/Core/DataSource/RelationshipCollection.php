<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DataSource;

/**
 * A RelationshipCollection represents a set of relationships of 
 * objects that implements ItemInterface.
 *
 * @api
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class RelationshipCollection implements \IteratorAggregate, \Countable
{
    private $relationships = [];

    /**
     * Returns an iterator over the relationships.
     * The key is the relationship's name and the
     * value is an array of Yosymfony\Spress\Core\DataSource\ItemInterface objects
     * with the item's id as key.
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over relationships.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->relationships);
    }

    /**
     * Gets the number of Relationships in this collection.
     *
     * @return int The number of relationships.
     */
    public function count()
    {
        return count($this->relationships);
    }

    /**
     * Adds a relationship.
     *
     * @param string                                         $name The relationship name
     * @param Yosymfony\Spress\Core\DataSource\ItemInterface $item An item.
     */
    public function add($name, ItemInterface $item)
    {
        if (isset($this->relationships[$name]) === false) {
            $this->relationships[$name] = [];
        }

        unset($this->relationships[$name][$item->getId()]);

        $this->relationships[$name][$item->getId()] = $item;
    }

    /**
     * Returns all relationships in this collection.
     *
     * @return array An array with the relationship's name as a key and an array of
     *               Yosymfony\Spress\Core\DataSource\ItemInterface object as elements
     *               with the item's id as key.
     */
    public function all()
    {
        return $this->relationships;
    }

    /**
     * Gets the items in a relationship.
     *
     * @param string $name The relationship name.
     *
     * @return Yosymfony\Spress\Core\DataSource\ItemInterface[]
     */
    public function get($name)
    {
        return isset($this->relationships[$name]) ? $this->relationships[$name] : [];
    }
    /**
     * Removes a relationship from the collection.
     *
     * @param string $name The relationship name.
     */
    public function remove($name, ItemInterface $item)
    {
        unset($this->relationships[$name][$item->getId()]);

        if (count($this->relationships[$name]) === 0) {
            unset($this->relationships[$name]);
        }
    }

    /**
     * Clears all relationship in this collection.
     */
    public function clear()
    {
        $this->relationships = [];
    }
}
