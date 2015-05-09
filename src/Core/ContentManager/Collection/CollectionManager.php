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

use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Collection manager
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class CollectionManager
{
    private $collections;
    private $defaultCollection;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->clear();
        $this->add(new Collection('pages', '', []));
    }

    /**
     * Add a new collection
     *
     * @param \Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface $collection
     */
    public function add(CollectionInterface $collection)
    {
        if ($this->has($collection->getName()) === true) {
            throw new \RuntimeException(sprintf('A previous collection exists with the same name: "%s".', $collection->getName()));
        }

        $this->collections[$collection->getName()] = $collection;
    }

    /**
     * Count the collections registered
     *
     * @return int
     */
    public function count()
    {
        return count($this->collections);
    }

    /**
     * Get a collection
     *
     * @param string $name
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface
     *
     * @throws \RuntimeException if collection not found
     */
    public function get($name)
    {
        if (false === $this->has($name)) {
            throw new \RuntimeException(sprintf('Collection: "%s" not found.', $name));
        }

        return $this->collections[$name];
    }

    /**
     * Has a collection with the name specified?
     *
     * @param string $name The collection's name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->collections[$name]);
    }

    /**
     * Clear the collections
     */
    public function clear()
    {
        $this->collections = [];
    }

    /**
     * Remove a collection
     *
     * @param string $name The collection's name
     */
    public function remove($name)
    {
        unset($this->collections[$name]);
    }

    /**
     * Collection matching of a item
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface
     */
    public function getCollectionForItem(ItemInterface $item)
    {
        foreach ($this->collections as $name => $collection) {
            $itemPath = $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE).'/';
            $collectionPath = $collection->getPath().'/';

            if (strpos($itemPath, $collectionPath) === 0) {
                return $collection;
            }
        }

        return $this->get('pages');
    }
}
