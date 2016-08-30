<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DataSource\Memory;

use Yosymfony\Spress\Core\DataSource\AbstractDataSource;
use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Memory data source. Useful for generating dynamic content.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class MemoryDataSource extends AbstractDataSource
{
    private $items = [];
    private $layouts = [];
    private $includes = [];

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * {@inheritdoc}
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * Adds a new item.
     *
     * @param Yosymfony\Spress\Core\DataSource\ItemInterface $item
     *
     * @throws RuntimeException If a previous item exists with the same id
     */
    public function addItem(ItemInterface $item)
    {
        if ($this->hasItem($item->getId()) === true) {
            throw new \RuntimeException(sprintf('A previous item exists with the same id: "%s".', $item->getId()));
        }

        $this->items[$item->getId()] = $item;
    }

    /**
     * Adds a new layout item.
     *
     * @param Yosymfony\Spress\Core\DataSource\ItemInterface $item
     *
     * @throws RuntimeException If a previous layout item exists with the same id
     */
    public function addLayout(ItemInterface $item)
    {
        if ($this->hasLayout($item->getId()) === true) {
            throw new \RuntimeException(sprintf('A previous layout item exists with the same id: "%s".', $item->getId()));
        }

        $this->layouts[$item->getId()] = $item;
    }

    /**
     * Adds a new include item.
     *
     * @param Yosymfony\Spress\Core\DataSource\ItemInterface $item
     *
     * @throws RuntimeException If a previous include item exists with the same id
     */
    public function addInclude(ItemInterface $item)
    {
        if ($this->hasInclude($item->getId()) === true) {
            throw new \RuntimeException(sprintf('A previous include item exists with the same id: "%s".', $item->getId()));
        }

        $this->includes[$item->getId()] = $item;
    }

    /**
     * Checks if an item exists.
     *
     * @param string $id The item's identifier
     *
     * @return bool
     */
    public function hasItem($id)
    {
        return isset($this->items[$id]);
    }

    /**
     * Checks if a layout item exists.
     *
     * @param string $id The item's identifier
     *
     * @return bool
     */
    public function hasLayout($id)
    {
        return isset($this->layouts[$id]);
    }

    /**
     * Checks if an include item exists.
     *
     * @param string $id The item's identifier
     *
     * @return bool
     */
    public function hasInclude($id)
    {
        return isset($this->includes[$id]);
    }

    /**
     * Sets an item.
     *
     * @param Yosymfony\Spress\Core\DataSource\ItemInterface $item
     */
    public function setItem(ItemInterface $item)
    {
        $this->items[$item->getId()] = $item;
    }

    /**
     * Sets a layout item.
     *
     * @param Yosymfony\Spress\Core\DataSource\ItemInterface $item
     */
    public function setLayout(ItemInterface $item)
    {
        $this->layouts[$item->getId()] = $item;
    }

    /**
     * Sets an include item.
     *
     * @param Yosymfony\Spress\Core\DataSource\ItemInterface $item
     */
    public function setInclude(ItemInterface $item)
    {
        $this->includes[$item->getId()] = $item;
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
     * Counts the layout items registered.
     *
     * @return int
     */
    public function countLayout()
    {
        return count($this->layouts);
    }

    /**
     * Counts the include items registered.
     *
     * @return int
     */
    public function countInclude()
    {
        return count($this->includes);
    }

    /**
     * Removes an item.
     *
     * @param string $id The item's identifier
     */
    public function removeItem($id)
    {
        unset($this->items[$id]);
    }

    /**
     * Removes a layout item.
     *
     * @param string $id The item's identifier
     */
    public function removeLayout($id)
    {
        unset($this->layouts[$id]);
    }

    /**
     * Removes a include item.
     *
     * @param string $id The item's identifier
     */
    public function removeInclude($id)
    {
        unset($this->includes[$id]);
    }

    /**
     * Clears all items registered.
     */
    public function clearItem()
    {
        $this->items = [];
    }

    /**
     * Clears all layouts registered.
     */
    public function clearLayout()
    {
        $this->layouts = [];
    }

    /**
     * Clears all includes registered.
     */
    public function clearInclude()
    {
        $this->includes = [];
    }
}
