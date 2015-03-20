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
 * File information
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Item implements ItemInterface 
{
    protected $id;
    protected $isBinary;
    protected $snapshot;

    /**
     * Constructor
     *
     * @param string $content
     * @param string $id
     * @param array $attributes
     * @param bool $isBinary
     */
	public function __construct($content, $id, array $attributes, $isBinary)
	{
        $this->snapshot = [];
        $this->setContent($content, self::SNAPSHOT_RAW);

        $this->id = $id;
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
        if($snapshotName) {
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
    }

    /**
     * @inheritDoc
     */
    public function setAttributes(array $value)
    {

    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {

    }

    /**
     * @inheritDoc
     */
    public function setPath($value)
    {

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
    	return 'item';
    }
}