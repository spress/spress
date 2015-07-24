<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DataWriter;

use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Memory data writer. It's used for testing purposes only.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class MemoryDataWriter implements DataWriterInterface
{
    protected $items;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setUp();
    }

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $this->items = [];
    }

    /**
     * @inheritDoc
     */
    public function write(ItemInterface $item)
    {
        if ($this->isWritable($item) === false) {
            return;
        }

        if ($item->isBinary()) {
            $this->items[$item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE)] = $item;
        } else {
            $this->items[$item->getPath(ItemInterface::SNAPSHOT_PATH_PERMALINK)] = $item;
        }
    }

    /**
     * @inheritDoc
     */
    public function tearDown()
    {
    }

    /**
     * Gets all items that has been written.
     *
     * @return \Yosymfony\Spress\Core\DataSource\ItemInterface[] The array's key is the path of the item.
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Check if we have a item.
     *
     * This method is for internal use only and should never be called directly.
     *
     * @param string $path Relative path of the content.
     *
     * @return bool
     */
    public function hasItem($path)
    {
        return isset($this->items[$path]);
    }

    /**
     * Gets the content that has been written.
     *
     * This method is for internal use only and should never be called directly.
     *
     * @param string $path Relative path of the content.
     *
     * @return \Yosymfony\Spress\Core\DataSource\ItemInterface
     */
    public function getItem($path)
    {
        return $this->items[$path];
    }

    /**
     * Number of items that have been written.
     *
     * This method is for internal use only and should never be called directly.
     *
     * @return int
     */
    public function countItems()
    {
        return count($this->items);
    }

    protected function isWritable(ItemInterface $item)
    {
        return $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE) === '' ? false : true;
    }
}
