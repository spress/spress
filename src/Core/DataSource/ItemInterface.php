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

    const TYPE_ITEM = 'item';
    const TYPE_LAYOUT = 'layout';
    const TYPE_INCLUDE = 'include';

    /**
     * A string that uniquely identifies an item.
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
     * @return string If the snapshot not exists return empty string.
     */
    public function getContent($snapshotName = '');

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
    public function getAttributes();

    /**
     * Set the attributes of this item
     *
     * @param array $value
     */
    public function setAttributes(array $values);

    /**
     * The item's relative path. e.g: "index.html" or "my-page/index.html".
     * An Item without path will not be stored.
     *
     * @return string
     */
    public function getPath();

    /**
     * Set the item's relative path.
     *
     * @param string $value e.g: "index.html" or "my-page/index.html"
     */
    public function setPath($value);

    /**
     * True if the item is binary; false if it is not.
     *
     * @return bool
     */
    public function isBinary();

    /**
     * Return the type of this item. Values: "item", "layout" or "include"
     *
     * @return string
     */
    public function getType();
}
