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
 * Collection manager.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class CollectionManager
{
    private $collections;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->clearCollection();
        $this->addCollection(new Collection('pages', '', []));
    }

    /**
     * Adds a new collection.
     *
     * @param \Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface $collection
     *
     * @throws RuntimeException If a previous collection exists with the same name.
     */
    public function addCollection(CollectionInterface $collection)
    {
        if ($this->hasCollection($collection->getName()) === true) {
            throw new \RuntimeException(sprintf(
                'A previous collection exists with the same name: "%s".',
                $collection->getName()));
        }

        $this->collections[$collection->getName()] = $collection;
    }

    /**
     * Counts the collections registered.
     *
     * @return int
     */
    public function countCollection()
    {
        return count($this->collections);
    }

    /**
     * Sets a collection.
     *
     * @param \Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface $collection
     */
    public function setCollection(CollectionInterface $collection)
    {
        $this->collections[$collection->getName()] = $collection;
    }

    /**
     * Gets a collection.
     *
     * @param string $name
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface
     *
     * @throws \RuntimeException If the collection is not defined.
     */
    public function getCollection($name)
    {
        if (false === $this->hasCollection($name)) {
            throw new \RuntimeException(sprintf('Collection: "%s" not found.', $name));
        }

        return $this->collections[$name];
    }

    /**
     * Checks if a collection exists.
     *
     * @param string $name The collection's name
     *
     * @return bool
     */
    public function hasCollection($name)
    {
        return isset($this->collections[$name]);
    }

    /**
     * Clears all collections registered.
     */
    public function clearCollection()
    {
        $this->collections = [];
    }

    /**
     * Removes a collection.
     *
     * @param string $name The collection's name.
     */
    public function remove($name)
    {
        unset($this->collections[$name]);
    }

    /**
     * Collection matching of a item.
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

        return $this->getCollection('pages');
    }
}
