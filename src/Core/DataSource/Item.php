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
 * File information.
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
        $this->collection = 'pages';

        $this->setContent($content, self::SNAPSHOT_RAW);

        $this->setAttributes($attributes);

        $this->setId($id);
        $this->type = $type;
        $this->isBinary = $isBinary;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCollection()
    {
        $this->collection;
    }

    /**
     * @inheritDoc
     */
    public function setCollection($name)
    {
        if (strlen($name) === 0) {
            throw new \RuntimeException('Invalid collection name. Expected a non-empty string.');
        }

        $this->collection = $name;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function setContent($content, $snapshotName)
    {
        $this->snapshot[$snapshotName] = $content;
        $this->snapshot[self::SNAPSHOT_LAST] = $content;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function setAttributes(array $values)
    {
        $this->attributes = $values;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function setPath($value, $snapshotName)
    {
        $this->pathSnapshot[$snapshotName] = $value;
        $this->pathSnapshot[self::SNAPSHOT_PATH_LAST] = $value;
    }

    /**
     * @inheritDoc
     */
    public function isBinary()
    {
        return $this->isBinary;
    }

    /**
     * @inheritDoc
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
