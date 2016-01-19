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
 * Item.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Item implements ItemInterface
{
    private $id;
    private $type;
    private $isBinary;
    private $snapshot;
    private $collection;
    private $attributes;
    private $pathSnapshot;

    /**
     * Constructor.
     *
     * @param string $content
     * @param string $id
     * @param array  $attributes
     * @param bool   $isBinary
     *
     * @throws RuntimeException If invalid id.
     */
    public function __construct($content, $id, array $attributes = [], $isBinary = false, $type = self::TYPE_ITEM)
    {
        $this->snapshot = [];
        $this->pathSnapshot = [];
        $this->attributes = [];

        $this->setContent($content, self::SNAPSHOT_RAW);
        $this->setAttributes($attributes);
        $this->setCollection('pages');
        $this->setId($id);

        $this->type = $type;
        $this->isBinary = $isBinary;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function setCollection($name)
    {
        if (strlen($name) === 0) {
            throw new \RuntimeException('Invalid collection name. Expected a non-empty string.');
        }

        $this->collection = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent($snapshotName = '')
    {
        if ($snapshotName) {
            if (isset($this->snapshot[$snapshotName]) === false) {
                return '';
            }

            return $this->snapshot[$snapshotName];
        }

        return isset($this->snapshot[self::SNAPSHOT_LAST]) === true ? $this->snapshot[self::SNAPSHOT_LAST] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content, $snapshotName)
    {
        $this->snapshot[$snapshotName] = $content;
        $this->snapshot[self::SNAPSHOT_LAST] = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $values)
    {
        $this->attributes = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($snapshotName = '')
    {
        if ($snapshotName) {
            if (isset($this->pathSnapshot[$snapshotName]) === false) {
                return '';
            }

            return $this->pathSnapshot[$snapshotName];
        }

        return isset($this->pathSnapshot[self::SNAPSHOT_PATH_LAST]) === true ? $this->pathSnapshot[self::SNAPSHOT_PATH_LAST] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($value, $snapshotName)
    {
        $this->pathSnapshot[$snapshotName] = $value;
        $this->pathSnapshot[self::SNAPSHOT_PATH_LAST] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function isBinary()
    {
        return $this->isBinary;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets id.
     *
     * @param string $id The item identifier.
     */
    protected function setId($id)
    {
        if (strlen($id) === 0) {
            throw new \RuntimeException('Invalid id. Expected a non-empty string.');
        }

        $this->id = $id;
    }
}
