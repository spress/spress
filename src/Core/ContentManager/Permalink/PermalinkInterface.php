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

/**
 * Iterface for a permanlink
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface PermalinkInterface
{
    /**
     * Relative URL path. The path must start with "/".
     * e.g: "/" or "/my-page"
     *
     * @return string
     */
    public function getUrlPath();

    /**
     * Relative file path. The path must not start with "/".
     * e.g: "index.html" or "my-page/index.html"
     *
     * @return string
     */
    public function getPath();
}
