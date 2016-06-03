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

use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Render event.
 *
 * Used with events:
 *   "spress.before_render_blocks",
 *   "spress.after_render_blocks",
 *   "spress.before_render_page",
 *   "spress.after_render_page".
 *
 * @api
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class RenderEvent extends ContentEvent
{
    /**
     * Gets the relative URL (only path component) of the item.
     *
     * @return string
     */
    public function getRelativeUrl()
    {
        $attributes = $this->getAttributes();

        return array_key_exists('url', $attributes) ? $attributes['url'] : '';
    }

    /**
     * Sets the relative URL (only path component) of the item.
     *
     * @param string $url The relative URL. e.g: /about/me/index.html
     */
    public function setRelativeUrl($url)
    {
        $url = $this->prepareUrl($url);
        $attributes = $this->getAttributes();

        $attributes['url'] = $url;
        $urlPath = $this->getPathFromUrl($url);

        $this->item->setPath($urlPath, ItemInterface::SNAPSHOT_PATH_PERMALINK);

        $this->setAttributes($attributes);
    }

    /**
     * Prepare a URL.
     *
     * @param string $url The relative URL.
     *
     * @return string
     *
     * @throws \RuntimeException If empty or malformed relative URL.
     */
    protected function prepareUrl($url)
    {
        if (empty($url)) {
            throw new \RuntimeException(sprintf('Empty URL at item with id: "%s"', $this->getId()));
        }

        if (stripos($url, '://') !== false) {
            throw new \RuntimeException(sprintf('Malformed relative URL at item with id: "%s"', $this->getId()));
        }

        if (stripos($url, '/') !== 0) {
            throw new \RuntimeException(sprintf('Relative URL must start with "/" at item with id: "%s"', $this->getId()));
        }

        return $url;
    }

    /**
     * Gets a path from a relative URL.
     *
     * @param string $relativeUrl Relative URL. e.g: /index.html
     *
     * @return string
     */
    protected function getPathFromUrl($relativeUrl)
    {
        return trim($relativeUrl, '/');
    }
}
