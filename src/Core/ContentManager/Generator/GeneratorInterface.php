<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Generator;

use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Iterface for generators.
 *
 * Generators are used for generating new items of content.
 * e.g: tags or categories for posts.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface GeneratorInterface
{
    /**
     * Generate items.
     *
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface $templateItem   The template item.
     * @param array                                           $siteAttributes The attribute of the site.
     *
     * @return Yosymfony\Spress\Core\DataSource\ItemInterface[]
     */
    public function generateItems(ItemInterface $templateItem, array $siteAttributes);
}
