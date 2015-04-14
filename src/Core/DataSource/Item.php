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
 * File information
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Item implements ItemInterface
{
    private $id;
    private $path;
    private $type;
    private $isBinary;
    private $snapshot;
    private $attributes;

    /**
     * Constructor
     *
     * @param string $content
     * @param string $id
     * @param array  $attributes
     * @param bool   $isBinary
     */
    public function __construct($content, $id, array $attributes = [], $isBinary = false, $type = self::TYPE_ITEM)
    {
        $this->snapshot = [];
        $this->attributes = [];

        $this->setContent($content, self::SNAPSHOT_RAW);

        $this->setAttributes($attributes);

        $this->id = $id;
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

    /**
     * @inheritDoc
     */
    public function getContent($snapshotName = '')
    {
        if ($snapshotName) {
            if (false == isset($this->snapshot[$snapshotName])) {
                return '';
            }

            return $this->snapshot[$snapshotName];
        }

        return $this->snapshot[self::SNAPSHOT_LAST];
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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function setPath($value)
    {
        $this->path = $value;
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
}
