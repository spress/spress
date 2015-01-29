<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Plugin\Event;

class ConvertEvent extends ContentEvent
{
    /**
     * Get the Front-matter
     *
     * @return array
     */
    public function getFrontmatter()
    {
        return $this->item->getFrontmatter()->getFrontmatterArray();
    }

    /**
     * Set the Front-matter
     *
     * @param array Front-matter
     */
    public function setFrontmatter(array $frontmatter)
    {
        $this->item->getFrontmatter()->setFrontmatter($frontmatter);
    }
}
