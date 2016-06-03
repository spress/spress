<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Plugin\Event;

use Symfony\Component\EventDispatcher\Event;
use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Content event.
 *
 * Used with events:
 *   - "spress.before_convert".
 *   - "spress.after_convert".
 *
 * @api
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ContentEvent extends Event
{
    protected $item;
    protected $defaultSnapshotContent;
    protected $defaultSnapshotPath;

    /**
     * Constructor.
     *
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface $item
     * @param string                                          $defaultSnapshotContent The name of the defatul snapshot for content.
     * @param string                                          $defaultSnapshotPath    The name of the defatul snapshot for path.
     */
    public function __construct(ItemInterface $item, $defaultSnapshotContent, $defaultSnapshotPath)
    {
        $this->item = $item;
        $this->defaultSnapshotContent = $defaultSnapshotContent;
        $this->defaultSnapshotPath = $defaultSnapshotPath;
    }

    /**
     * Gets the item identifier.
     *
     * @return string
     */
    public function getId()
    {
        return $this->item->getId();
    }

    /**
     * Gets the content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->item->getContent($this->defaultSnapshotContent);
    }

    /**
     * Sets the content (without Front-matter).
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->item->setContent($content, $this->defaultSnapshotContent);
    }

    /**
     * Gets the attributes of the item.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->item->getAttributes();
    }

    /**
     * Sets the attributes of the item.
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        return $this->item->setAttributes($attributes);
    }

    /**
     * Gets the item.
     *
     * @return \Yosymfony\Spress\Core\DataSource\ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }
}
