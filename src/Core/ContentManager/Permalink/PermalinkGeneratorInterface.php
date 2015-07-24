<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Permalink;

use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Iterface for a permanlink.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface PermalinkGeneratorInterface
{
    /**
     * Gets a permalink.
     *
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface $item
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkInterface
     */
    public function getPermalink(ItemInterface $item);
}
