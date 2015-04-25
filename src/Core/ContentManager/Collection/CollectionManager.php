<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Collection;

use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Collection manager
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class CollectionManager
{
    private $collections;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->clear();
    }

    public function add(CollectionInterface $collection)
    {
        if ($this->has($collection->getName()) === true) {
            throw new \RuntimeException(sprintf('A previous collection exists with the same name: "%s".', $collection->getName()));
        }

        $this->collections[$name] = $collection;
    }

    public function remove($name)
    {
        unset($this->collections[$name]);
    }

    public function has($name)
    {
        return isset($this->collections[$name]);
    }

    public function clear()
    {
        $this->collections = [];
    }

    public function getCollectionForItem(ItemInterface $item)
    {
    }
}
