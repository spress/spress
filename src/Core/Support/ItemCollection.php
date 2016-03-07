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

use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * A ItemCollection represents a collection of items.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ItemCollection extends Collection
{
    private $itemCollections = [];
    private $idCollections = [];

    /**
     * Constructor.
     * Initialize the collection using the item's id of each eatem as key.
     *
     * Yosymfony\Spress\Core\DataSource\ItemInterface[] $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item->getId(), $item);
        }
    }

    /**
     * Sets an element.
     *
     * @param string $key The key associated to the element.
     * @param Yosymfony\Spress\Core\DataSource\ItemInterface
     *
     * @throws \RuntimeException If the item has been registered previously in another collection.
     */
    public function set($key, ItemInterface $element)
    {
        $collectionName = $element->getCollection();

        parent::set($key, $element);

        if (isset($this->idCollections[$key]) && $this->idCollections[$key] !== $collectionName) {
            throw new \RuntimeException(sprintf('The item with id: "%s" has been registered previously with another collection.', $key));
        }

        if (isset($this->itemCollections[$collectionName]) === false) {
            $this->itemCollections[$collectionName] = [];
        }

        $this->itemCollections[$collectionName][$key] = $element;
        $this->idCollections[$key] = $collectionName;
    }

    /**
     * Returns all elemts in this collection.
     * 
     * @param string[] $collections       The name of the item collections affected. 
     *                                    Array empty means all.
     * @param bool     $groupByCollection First level of array is the collection name.
     * 
     * @return array
     */
    public function all(array $collections = [], $groupByCollection = false)
    {
        if (count($collections) === 0) {
            if ($groupByCollection === false) {
                return parent::all();
            }

            return $this->itemCollections;
        }

        $result = [];

        foreach ($collections as $collection) {
            if (isset($this->itemCollections[$collection]) === false) {
                continue;
            }

            if ($groupByCollection === false) {
                $result = array_merge($result, $this->itemCollections[$collection]);

                continue;
            }

            $result[$collection] = $this->itemCollections[$collection];
        }

        return $result;
    }

    /**
     * Sorts items in this collection.
     * e.g:.
     * 
     * ```
     * $itemCollection = new ItemCollection();
     * // warm-up...
     * $items = $itemCollection->sortItems('date', true)->all();
     * ```
     * 
     * @param string   $attribute   The name of the attribute used to sort.
     * @param bool     $descending  Is descending sort?
     * @param string[] $collections Only the items belong to Collections will be affected.
     *
     * @return \Yosymfony\Spress\Core\Support\ItemCollection An intance of itself.
     */
    public function sortItems($attribute, $descending = true, array $collections = [])
    {
        $itemCollections = $this->all($collections, true);

        $callback = function ($key, ItemInterface $item) use ($attribute) {
            $attributes = $item->getAttributes();

            return isset($attributes[$attribute]) === true ? $attributes[$attribute] : null;
        };

        foreach ($itemCollections as $collection => $items) {
            $arr = new ArrayWrapper($items);
            $itemsSorted = $arr->sortBy($callback, null, SORT_REGULAR, $descending);
            $this->itemCollections[$collection] = $itemsSorted;
        }

        $this->elements = [];

        foreach ($this->itemCollections as $collection => $items) {
            $this->elements = array_merge($this->elements, $items);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        if ($this->has($key) === false) {
            return;
        }

        $collection = $this->idCollections[$key];

        unset($this->idCollections[$key]);
        unset($this->itemCollections[$collection]);

        parent::remove($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->itemCollections = [];
        $this->idCollections = [];

        parent::clear();
    }
}
