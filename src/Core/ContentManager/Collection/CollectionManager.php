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
    private $colectionItemCollection;

    /**
     * Constructor.
     * Inserts the "pages" collection as default collection.
     */
    public function __construct()
    {
        $this->colectionItemCollection = new ColectionItemCollection();
        $this->colectionItemCollection->add(new Collection('pages', ''));
    }

    /**
     * Gets the collection-item collection.
     * 
     * @return Yosymfony\Spress\Core\ContentManager\Collection\ColectionItemCollection
     */
    public function getCollectionItemCollection()
    {
        return $this->colectionItemCollection;
    }

    /**
     * Collection matching of a item.
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface
     */
    public function getCollectionForItem(ItemInterface $item)
    {
        foreach ($this->colectionItemCollection as $name => $collection) {
            $itemPath = $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE).'/';
            $collectionPath = $collection->getPath().'/';

            if (strpos($itemPath, $collectionPath) === 0) {
                return $collection;
            }
        }

        return $this->colectionItemCollection->get('pages');
    }
}
