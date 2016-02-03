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
 * Item set.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ItemSet
{
    private $items;
    private $itemCollections;
    private $idCollections;

    /**
     * Constructor.
     *
     * \Yosymfony\Spress\Core\DataSource\ItemInterface[] $items
     */
    public function __construct(array $items = [])
    {
        $this->clearItem();

        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    /**
     * Adds an new item.
     * 
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface $item
     */
    public function addItem(ItemInterface $item)
    {
        if ($this->hasItem($item->getId()) === true) {
            throw new \RuntimeException(sprintf('A previous item exists with the same id: "%s".', $item->getId()));
        }

        $this->setItem($item);
    }

    /**
     * Sets an item.
     * 
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface
     */
    public function setItem(ItemInterface $item)
    {
        $id = $item->getId();
        $collectionName = $item->getCollection();

        $this->items[$id] = $item;

        if (isset($this->idCollections[$id]) && $this->idCollections[$id] !== $collectionName) {
            throw new \RuntimeException(sprintf('The item with id: "%s" has been registered previously with another collection.', $id));
        }

        if (isset($this->itemCollections[$collectionName]) === false) {
            $this->itemCollections[$collectionName] = [];
        }

        $this->itemCollections[$collectionName][$id] = $item;
        $this->idCollections[$id] = $collectionName;
    }

    /**
     * Counts the items registered.
     *
     * @return int
     */
    public function countItem()
    {
        return count($this->items);
    }

    /**
     * Gets an item.
     * 
     * @param string $id Identifier of the item.
     * 
     * @return \Yosymfony\Spress\Core\DataSource\ItemInterface
     */
    public function getItem($id)
    {
        if (false === $this->hasItem($id)) {
            throw new \RuntimeException(sprintf('Item with id: "%s" not found.', $id));
        }

        return $this->items[$id];
    }

    /**
     * Checks if a item exists.
     *
     * @param string $id The item's name.
     *
     * @return bool
     */
    public function hasItem($id)
    {
        return isset($this->items[$id]);
    }

    /**
     * Gets a list of items registered.
     * 
     * @param string[] $collections       The name of the collections affected. 
     *                                    Array empty means all.
     * @param bool     $groupByCollection First level of array is the collection name.
     * 
     * @return array
     */
    public function getItems(array $collections = [], $groupByCollection = false)
    {
        if (count($collections) === 0) {
            if ($groupByCollection === false) {
                return $this->items;
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
     * Sorts items.
     * e.g:.
     * 
     * ```
     * $iSet = new ItemSet();
     * // warm-up...
     * $iset->sortItems('date', true);
     * $items = $iSet->getItems();
     * ```
     * 
     * @param string   $attribute   The name of the attribute used to sort.
     * @param bool     $descending  Is descending sort?
     * @param string[] $collections Only the items belong to Collections will be affected.
     */
    public function sortItems($attribute, $descending = true, array $collections = [])
    {
        $itemCollections = $this->getItems($collections, true);

        $callback = function ($key, ItemInterface $item) use ($attribute) {
            $attributes = $item->getAttributes();

            return isset($attributes[$attribute]) === true ? $attributes[$attribute] : null;
        };

        foreach ($itemCollections as $collection => $items) {
            $arr = new ArrayWrapper($items);
            $itemsSorted = $arr->sortBy($callback, null, SORT_REGULAR, $descending);
            $this->itemCollections[$collection] = $itemsSorted;
        }

        $this->items = [];

        foreach ($this->itemCollections as $collection => $items) {
            $this->items = array_merge($this->items, $items);
        }
    }

    /**
     * Removes an item.
     * 
     * @param string $id Identifier of the item.
     */
    public function removeItem($id)
    {
        if ($this->hasItem($id) === false) {
            return;
        }

        $collection = $this->idCollections[$id];

        unset($this->idCollections[$id]);
        unset($this->itemCollections[$collection]);
        unset($this->items[$id]);
    }

    /**
     * Clears all items registered.
     */
    public function clearItem()
    {
        $this->items = [];
        $this->itemCollections = [];
        $this->idCollections = [];
    }
}
