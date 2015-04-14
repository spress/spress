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
 * Permanlink
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Permalink implements PermalinkInterface
{
    private $path;
    private $urlPath;

    public function __construct($path, $urlPath)
    {
        $this->path = $path;
        $this->urlPath = $urlPath;
    }

    /**
     * @inheritDoc
     */
    public function getUrlPath()
    {
        return $this->urlPath;
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return $this->path;
    }
}
