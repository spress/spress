<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Datasource;

/**
 * Iterface for a data item
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface ItemInterface
{
    const SNAPSHOT_RAW = 'raw';
    const SNAPSHOT_LAST = 'last';
    const SNAPSHOT_AFTER_CONVERT = 'after_convert';
    const SNAPSHOT_AFTER_RENDER = 'after_render';

    /**
     * A string that uniquely identifies an item. Identifiers start
     * and end with a slash. e.g: /posts/post1/ or /posts/post1/index.html/.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the compiled content. A snapshot is the compiled content at a
     * specific point during the compilation process. "last" snapshot is
     * the most recent compiled content. Binary content cannot have snapshots.
     *
     * Snaptshots:
     *  - raw: the uncompiled content of this item (not available for binary items).
     *  - last: the most recent compiled content.
     *  - after_convert: the compiled content after converter has been applied.
     *  - after_render: the compiled content after renderizer has been applied.
     *
     * @param $snapshotName The name of the snapshot. "last" by default.
     *
     * @return Mixed
     */
    public function getContent($snapshotName);

    /**
     * Set the compiled content.
     *
     * @param $content Mixed The compiled content.
     * @param $snapshotName The name of the snapshot. The snapshot "last" is not valid.
     */
    public function setContent($content, $snapshotName);

    /**
     * The item’s attributes.
     *
     * @return array
     */
    public function getFrontmatter();

    /**
     * The item's path. e.g: / for the home or /post/post1
     *
     * @return string
     */
    public function getPath();

    /**
     * Set the item's path.
     *
     * @param string $value e.g: / or /my-page.html
     */
    public function setPath($value);

    /**
     * True if the item is binary; false if it is not.
     *
     * @return @bool
     */
    public function isBinary();

    /**
     * The time when this item was last modified.
     *
     * @return string
     */
    public function getModifiedTime();

    /**
     * Return the type of this item. Values: "item" or "layout"
     *
     * @return string
     */
    public function getType();
}
