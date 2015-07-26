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

/**
 * Iterface for a collection.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface CollectionInterface
{
    /**
     * The collection's name.
     *
     * @return string
     */
    public function getName();

    /**
     * The collection attributes. Each item of the collection
     * will automatically receive this attributes.
     *
     * return array
     */
    public function getAttributes();

    /**
     * The collection's relative path. e.g: "_events" or "books/top".
     *
     * return @string
     */
    public function getPath();
}
