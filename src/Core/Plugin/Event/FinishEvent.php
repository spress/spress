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

/**
 * Finish event.
 *
 * Used with events:
 *   - "spress.finish".
 */
class FinishEvent extends Event
{
    protected $items;
    protected $siteAttributes;

    /**
     * Constructor.
     *
     * @param array $items
     * @param array $siteAttributes The site attributes used for rendering the site.
     */
    public function __construct(array $items, array $siteAttributes)
    {
        $this->items = $items;
        $this->siteAttributes = $siteAttributes;
    }

    /**
     * Gets items.
     *
     * @return \Yosymfony\Spress\Core\DataSource\ItemInterface[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Gets site attributes.
     *
     * @return array
     */
    public function getSiteAttributes()
    {
        return $this->siteAttributes;
    }
}
