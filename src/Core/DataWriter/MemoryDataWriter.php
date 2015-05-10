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
 * This data writer uses SNAPSHOT_PATH_PERMALINK for working
 * with paths of items.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class MemoryDataWriter implements DataWriterInterface
{
    protected $items;

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
        $this->items[$item->getPath(ItemInterface::SNAPSHOT_PATH_PERMALINK)] = $item->getContent();
    }

    /**
     * @inheritDoc
     */
    public function tearDown()
    {
    }

    /**
     * Check if we have a item.
     *
     * This method is for internal use only and should never be called directly.
     *
     * @param string $path Relative path of the content
     *
     * @return bool
     */
    public function existsItem($path)
    {
        return isset($this->items[$path]);
    }

    /**
     * Gets the content that has been written.
     *
     * This method is for internal use only and should never be called directly.
     *
     * @param bool $path Relative path of the content
     *
     * @return string
     */
    public function getContentItem($path)
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
}
