<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Generator\Pagination;

use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Pagination item.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PaginationItem extends Item
{
	protected $pageItems = [];

	/**
     * @inheritDoc
     */
    public function getAttributes()
    {
        $attributes = parent::getAttributes();

        if (isset($attributes['pagination']) === false) {
        	return $attributes;
        }

        $attributes['pagination']['items'] = [];

        foreach ($this->pageItems as $item) {
			$attributes['pagination']['items'][$item->getId()] = $this->getItemAttributes($item);       	
        }

        return $attributes;
    }

    /**
     * Sets the items of a page.
     * 
     * @param ItemInterface[] $items
     */
    public function setPageItems(array $items)
	{
		$this->pageItems = $items;
	}

	/**
	 * Gets the attributes of an item.
	 * 
	 * @param  ItemInterface $item
	 * 
	 * @return array
	 */
    protected function getItemAttributes(ItemInterface $item)
    {
        $result = $item->getAttributes();

        $result['id'] = $item->getId();
        $result['content'] = $item->getContent();
        $result['collection'] = $item->getCollection();
        $result['path'] = $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE);

        return $result;
    }
}