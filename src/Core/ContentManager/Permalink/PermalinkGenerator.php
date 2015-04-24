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
use Yosymfony\Spress\Core\Exception\AttributeValueException;
use Yosymfony\Spress\Core\Utils;

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
    private $defaultPreservePathTitle;

    /**
     * Constructor
     *
     * @param string $defaultPermalink
     *
     *   Each item's URL are prefixed by "/:collection" if the item are included in a custom collection.
     *
     *   "pretty" permalink style:
     *    - item: "/:path/:basename"
     *    - item with date: "/:categories/:year/:month/:day/:title/"
     *
     *   "date" permalink style:
     *    - item: "/:path/:basename.:extension"
     *    - item with date: "/:categories/:year/:month/:day/:title.:extension"
     *
     *   "ordinal" permalink style:
     *    - item: "/:path/:basename.:extension"
     *    - item with date: "/:categories/:year/:i_day/:title.:extension"
     *
     *   "none" permalink style:
     *    - item: "/:path/:basename.:extension"
     *
     */
    public function __construct($defaultPermalink = 'pretty', $defaultPreservePathTitle = false)
    {
        $this->defaultPermalink = $defaultPermalink;
        $this->defaultPreservePathTitle = $defaultPreservePathTitle;
    }

    /**
     * Get a permalink.
     *
     * Item's attributes with special meaning:
     *  - title: title of the item.
     *  - title_path: title extracted from the date filename pattern.
     *  - preserve_path_title: if true "title_path" instead of "title" will be used with ":title" placeholder.
     *  - date: date of item.
     *  - categories: categories for the item
     *  - permalink: permalink sytle.
     *  - collection: the name of the item's collection.
     *
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface $item
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkInterface
     */
    public function getPermalink(ItemInterface $item)
    {
        $placeholders = $this->getPlacehoders($item);
        $permalinkStyle = $this->getPermalinkAttribute($item);

        switch ($permalinkStyle) {
            case 'none':
                $urlTemplate = '/:path/:basename.:extension';

                if ($this->isCustomCollection($item)) {
                    $urlTemplate = '/:collection'.$urlTemplate;
                }

                $pathTemplate = $urlTemplate;
                break;
            case 'ordinal':
                if ($this->isItemWithDate($item)) {
                    $urlTemplate = '/:categories/:year/:i_day/:title.:extension';
                } else {
                    $urlTemplate = '/:path/:basename.:extension';
                }

                if ($this->isCustomCollection($item)) {
                    $urlTemplate = '/:collection'.$urlTemplate;
                }

                $pathTemplate = $urlTemplate;
                break;
            case 'date':
                if ($this->isItemWithDate($item)) {
                    $urlTemplate = '/:categories/:year/:month/:day/:title.:extension';
                } else {
                    $urlTemplate = '/:path/:basename.:extension';
                }

                if ($this->isCustomCollection($item)) {
                    $urlTemplate = '/:collection'.$urlTemplate;
                }

                $pathTemplate = $urlTemplate;
                break;
            case 'pretty':
                if ($placeholders[':extension'] !== 'html') {
                    $urlTemplate = '/:path/:basename.:extension';
                    $pathTemplate = $urlTemplate;

                    if ($this->isCustomCollection($item)) {
                        $urlTemplate = '/:collection'.$urlTemplate;
                        $pathTemplate = '/:collection'.$pathTemplate;
                    }
                    break;
                }

                if ($this->isItemWithDate($item)) {
                    $urlTemplate = '/:categories/:year/:month/:day/:title';
                    $pathTemplate = '/:categories/:year/:month/:day/:title/index.html';
                } else {
                    if ($placeholders[':basename'] === 'index') {
                        $urlTemplate = '/:path';
                        $pathTemplate = '/:path/index.html';
                    } else {
                        $urlTemplate = '/:path/:basename';
                        $pathTemplate = '/:path/:basename/index.html';
                    }
                }

                if ($this->isCustomCollection($item)) {
                    $urlTemplate = '/:collection'.$urlTemplate;
                    $pathTemplate = '/:collection'.$pathTemplate;
                }
                break;
            default:
                $urlTemplate = $permalinkStyle;
                $pathTemplate = $urlTemplate;
                break;
        }

        $path = $this->generatePath($pathTemplate, $placeholders);
        $urlPath = $this->generateUrlPath($urlTemplate, $placeholders);

        return new Permalink($path, $urlPath);
    }

    private function getPlacehoders(ItemInterface $item)
    {
        $fileInfo = new \SplFileInfo($item->getPath());
        $time = $this->getDateAttribute($item);

        $result = [
            ':path'         => $fileInfo->getPath(),
            ':extension'    => $fileInfo->getExtension(),
            ':basename'     => $fileInfo->getBasename('.'.$fileInfo->getExtension()),
            ':collection'   => $this->getCollectionAttribute($item),
            ':categories'   => $this->getCategoriesPath($item),
            ':title'        => $this->getTitleSlugified($item),
            ':year'         => $time->format('Y'),
            ':month'        => $time->format('m'),
            ':day'          => $time->format('d'),
            ':i_month'      => $time->format('n'),
            ':i_day'        => $time->format('j'),
        ];

        return $result;
    }

    private function isItemWithDate(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        if (isset($attributes['date']) === true) {
            return true;
        }

        return false;
    }

    private function getTitleSlugified(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        $preservePathTitle = $this->getPreservePathTitleAttribute($item);

        if ($preservePathTitle === true && isset($attributes['title_path']) === true) {
            return Utils::slugify($attributes['title_path']);
        }

        if (isset($attributes['title']) === true) {
            return Utils::slugify($attributes['title']);
        }
    }

    private function getCategoriesPath(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        if (isset($attributes['categories']) === false) {
            return;
        }

        if (is_array($attributes['categories']) === false) {
            throw new AttributeValueException('Invalid value. Expected array.', 'categories', $item->getPath());
        }

        return implode('/', array_map(function ($a) {
            return Utils::slugify($a);
        }, $attributes['categories']));
    }

    private function generatePath($template, array $placeholders = [])
    {
        return ltrim($this->generateUrlPath($template, $placeholders), '/');
    }

    private function generateUrlPath($template, array $placeholders = [])
    {
        if (0 == strlen($template)) {
            throw new \InvalidArgumentException('The template param must be a template or a URL');
        }

        $permalink = str_replace(array_keys($placeholders), $placeholders, $template, $count);

        return $this->sanitize($permalink);
    }

    private function getPreservePathTitleAttribute(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        if (isset($attributes['preserve_path_title']) === true) {
            if (is_bool($attributes['preserve_path_title']) === false) {
                throw new AttributeValueException('Invalid value. Expected bolean', 'preserve_path_title', $item->getPath());
            }

            return $attributes['preserve_path_title'];
        }

        return $this->defaultPreservePathTitle;
    }

    private function getDateAttribute(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        if (isset($attributes['date']) === true) {
            try {
                return new \DateTime($attributes['date']);
            } catch (\Exception $e) {
                throw new AttributeValueException('Invalid value. Expected date string', 'date', $item->getPath());
            }
        }

        return new \DateTime();
    }

    private function isCustomCollection(ItemInterface $item)
    {
        $collection = $this->getCollectionAttribute($item);

        return !in_array($collection, ['posts', 'pages']);
    }

    private function getPermalinkAttribute(ItemInterface $item)
    {
        $attributes = $item->getAttributes();
        $permalink = isset($attributes['permalink']) ? $attributes['permalink'] : $this->defaultPermalink;

        if (is_string($permalink) === false) {
            throw new AttributeValueException('Invalid value. Expected string.', 'permalink', $item->getPath());
        }

        if (trim($permalink) === '') {
            throw new AttributeValueException('Invalid value. Expected a non-empty value.', 'permalink', $item->getPath());
        }

        return $permalink;
    }

    private function getCollectionAttribute(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        if (isset($attributes['collection']) === false) {
            return;
        }

        if (is_string($attributes['collection']) === false) {
            throw new AttributeValueException('Invalid value. Expected string.', 'collection', $item->getPath());
        }

        if (trim($attributes['collection']) === '') {
            throw new AttributeValueException('Invalid value. Expected a non-empty value.', 'collection', $item->getPath());
        }

        return $attributes['collection'];
    }

    private function sanitize($url)
    {
        $count = 0;
        $result = preg_replace('/\/\/+/', '/', $url);
        $result = str_replace(':/', '://', $result, $count);

        if ($result !== '/') {
            $result = rtrim($result, '/');
        }

        if ($count > 1) {
            throw new \UnexpectedValueException(sprintf('Bad URL: "%s"', $result));
        }

        if (false !== strpos($result, ' ')) {
            throw new \UnexpectedValueException(sprintf('Bad URL: "%s". Contain white space/s', $result));
        }

        return $result;
    }
}
