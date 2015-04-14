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
 * Iterface for a permalink generator.
 * e.g: /my-page/about-me.html
 *
 * Placeholders:
 *  - ":path"		: /my-page
 *  - ":basename"	: about-me
 *  - ":extension"	: html
 *
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PermalinkGenerator
{
    private $defaultPermalink;

    /**
     * Constructor
     *
     * @param string $defaultPermalink
     *
     *   "pretty" permalink style:
     *    - pages: "/:path/:basename".
     *    - posts: "/:categories/:year/:month/:day/:title/".
     *    - collections: "/:collection/:path".
     *
     */
    public function __construct($defaultPermalink = 'pretty')
    {
        $this->defaultPermalink = $defaultPermalink;
    }

    /**
     * Get a permalink
     *
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface $item
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkInterface
     */
    public function getPermalink(ItemInterface $item)
    {
        $placeholders = $this->getPlacehoders($item);

        switch ($this->getPermalinkAttribute($item)) {
            case 'path':
                $path = $item->getPath();
                $urlPath = '/'.$path;
                break;
            case 'ordinal':
                break;
            case 'date':
                break;
            default:
                $urlTemplate = '/:path/:basename.:extension';
                $pathTemplate = $urlTemplate;

                if ($placeholders[':extension'] === 'html') {
                    if ($placeholders[':basename'] === 'index') {
                        $urlTemplate = '/:path';
                        $pathTemplate = '/:path/index.html';
                    } else {
                        $urlTemplate = '/:path/:basename';
                        $pathTemplate = '/:path/:basename/index.html';
                    }
                }

                $path = ltrim($this->generateUrlPath($pathTemplate, $placeholders), '/');
                $urlPath = $this->generateUrlPath($urlTemplate, $placeholders);
                break;
        }

        return new Permalink($path, $urlPath);
    }

    private function getPlacehoders(ItemInterface $item)
    {
        $fileInfo = new \SplFileInfo($item->getPath());

        return [
            ':path'            => $fileInfo->getPath(),
            ':extension'    => $fileInfo->getExtension(),
            ':basename'     => $fileInfo->getBasename('.'.$fileInfo->getExtension()),
        ];
    }

    private function isPostPath(ItemInterface $item)
    {
        return false;
    }

    private function isCollection(ItemInterface $item)
    {
        return false;
    }

    private function generateUrlPath($template, array $placeholders = [])
    {
        if (0 == strlen($template)) {
            throw new \InvalidArgumentException('The template param must be a template or a URL');
        }

        $permalink = str_replace(array_keys($placeholders), $placeholders, $template, $count);

        return $this->sanitize($permalink);
    }

    private function sanitize($url)
    {
        $count = 0;
        $result = preg_replace('/\/\/+/', '/', $url);
        $result = str_replace(':/', '://', $result, $count);

        if ($count > 1) {
            throw new \UnexpectedValueException(sprintf('Bad URL: "%s"', $result));
        }

        if (false !== strpos($result, ' ')) {
            throw new \UnexpectedValueException(sprintf('Bad URL: "%s". Contain white space/s', $result));
        }

        return $result;
    }

    private function getPermalinkAttribute(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        if (isset($attributes['permalink']) && $attributes['permalink']) {
            return $attributes['permalink'];
        }

        return $this->defaultPermalink;
    }
}
