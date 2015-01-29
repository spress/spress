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

use Symfony\Component\EventDispatcher\Event;

class AfterConvertPostsEvent extends Event
{
    private $categories;
    private $tags;

    public function __construct(array $categories, array $tags)
    {
        $this->categories = $categories;
        $this->tags = $tags;
    }

    /**
     * Get categories of posts
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Get tags of posts
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }
}
