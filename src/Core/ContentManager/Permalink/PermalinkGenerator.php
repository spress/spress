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
use Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException;
use Yosymfony\Spress\Core\ContentManager\Exception\MissingAttributeException;
use Yosymfony\Spress\Core\Support\StringWrapper;

/**
 * Iterface for a permalink generator. e.g: /my-page/about-me.html.
 *
 * Attributes with special meaning:
 *  - permalink: (string) The permalink template.
 *  - preserve_path_title: (bool)
 *  - date: (string)
 *  - categories: (array)
 *
 * Placeholders:
 *  - ":path"		: /my-page
 *  - ":basename"	: about-me
 *  - ":extension"	: html
 *
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PermalinkGenerator implements PermalinkGeneratorInterface
{
    /**
     * Predefined permalink 'none'
     */
    const PERMALINK_NONE = '/:path/:basename.:extension';

    private $defaultPermalink;
    private $defaultPreservePathTitle;

    /**
     * Constructor.
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
     * @param bool $defaultPreservePathTitle Default value for Preserve-path-title.
     */
    public function __construct($defaultPermalink = 'pretty', $defaultPreservePathTitle = false)
    {
        $this->defaultPermalink = $defaultPermalink;
        $this->defaultPreservePathTitle = $defaultPreservePathTitle;
    }

    /**
     * Gets a permalink.
     *
     * For binary items URL path and path point to SNAPSHOT_PATH_RELATIVE.
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
        if ($item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE) === '') {
            return new Permalink('', '');
        }

        $placeholders = $this->getPlacehoders($item);
        $permalinkStyle = $this->getPermalinkAttribute($item);

        if ($item->isBinary() === true) {
            $urlTemplate = $this::PERMALINK_NONE;
            $path = $this->generatePath($urlTemplate, $placeholders);
            $urlPath = $this->generateUrlPath($urlTemplate, $placeholders);

            return new Permalink($path, $urlPath);
        }

        switch ($permalinkStyle) {
            case 'none':
                $urlTemplate = $this::PERMALINK_NONE;
                $pathTemplate = $urlTemplate;
                break;
            case 'ordinal':
                if ($this->isItemWithDate($item)) {
                    $urlTemplate = '/:categories/:year/:i_day/:title.:extension';

                    if ($this->isCustomCollection($item)) {
                        $urlTemplate = '/:collection'.$urlTemplate;
                    }
                } else {
                    $urlTemplate = $this::PERMALINK_NONE;
                }

                $pathTemplate = $urlTemplate;
                break;
            case 'date':
                if ($this->isItemWithDate($item)) {
                    $urlTemplate = '/:categories/:year/:month/:day/:title.:extension';

                    if ($this->isCustomCollection($item)) {
                        $urlTemplate = '/:collection'.$urlTemplate;
                    }
                } else {
                    $urlTemplate = $this::PERMALINK_NONE;
                }

                $pathTemplate = $urlTemplate;
                break;
            case 'pretty':
                if ($this->isItemWithDate($item)) {
                    $urlTemplate = '/:categories/:year/:month/:day/:title';
                    $pathTemplate = '/:categories/:year/:month/:day/:title/index.html';

                    if ($this->isCustomCollection($item)) {
                        $urlTemplate = '/:collection'.$urlTemplate;
                        $pathTemplate = '/:collection'.$pathTemplate;
                    }
                } else {
                    if ($placeholders[':extension'] !== 'html') {
                        $urlTemplate = $this::PERMALINK_NONE;
                        $pathTemplate = $urlTemplate;
                    } else {
                        if ($placeholders[':basename'] === 'index') {
                            $urlTemplate = '/:path';
                            $pathTemplate = '/:path/index.html';
                        } else {
                            $urlTemplate = '/:path/:basename';
                            $pathTemplate = '/:path/:basename/index.html';
                        }
                    }
                }
                break;
            default:
                if (!$this->templateNeedsDate($permalinkStyle)
                    || $this->isItemWithDate($item)) {
                    $urlTemplate = $permalinkStyle;
                } else {
                    $urlTemplate = $this::PERMALINK_NONE;
                }
                $pathTemplate = $urlTemplate;
                break;
        }

        $path = $this->generatePath($pathTemplate, $placeholders);
        $urlPath = $this->generateUrlPath($urlTemplate, $placeholders);

        return new Permalink($path, $urlPath);
    }

    private function getPlacehoders(ItemInterface $item)
    {
        $fileInfo = new \SplFileInfo($item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));

        $result = [
            ':path' => (new StringWrapper($fileInfo->getPath()))->deletePrefix('.'),
            ':extension' => $fileInfo->getExtension(),
            ':basename' => $fileInfo->getBasename('.'.$fileInfo->getExtension()),
            ':collection' => $item->getCollection(),
            ':categories' => $this->getCategoriesPath($item),
            ':title' => $this->getTitleSlugified($item),
        ];

        if ($this->isItemWithDate($item)) {
            $time = $this->getDateAttribute($item);
            $result += [
                ':year' => $time->format('Y'),
                ':month' => $time->format('m'),
                ':day' => $time->format('d'),
                ':i_month' => $time->format('n'),
                ':i_day' => $time->format('j'),
            ];
        }

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

    private function templateNeedsDate($template)
    {
        return strpos($template, ':year') !== false
            || strpos($template, ':month') !== false
            || strpos($template, ':day') !== false
            || strpos($template, ':i_month') !== false
            || strpos($template, ':i_day') !== false;
    }

    private function getTitleSlugified(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        $preservePathTitle = $this->getPreservePathTitleAttribute($item);

        if ($preservePathTitle === true && isset($attributes['title_path']) === true) {
            return (new StringWrapper($attributes['title_path']))->slug();
        }

        if (isset($attributes['title']) === true) {
            return (new StringWrapper($attributes['title']))->slug();
        }
    }

    private function getCategoriesPath(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        if (isset($attributes['categories']) === false) {
            return;
        }

        if (is_array($attributes['categories']) === false) {
            throw new AttributeValueException('Invalid value. Expected array.', 'categories', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }

        return implode('/', array_map(function ($a) {
            return (new StringWrapper($a))->slug();
        }, $attributes['categories']));
    }

    private function generatePath($template, array $placeholders = [])
    {
        $path = $this->generateUrlPath($template, $placeholders);

        return ltrim($path, '/');
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
                throw new AttributeValueException('Invalid value. Expected bolean', 'preserve_path_title', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
            }

            return $attributes['preserve_path_title'];
        }

        return $this->defaultPreservePathTitle;
    }

    private function getDateAttribute(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        if (isset($attributes['date']) === false) {
            throw new MissingAttributeException('Attribute date required', 'date', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }

        if (is_string($attributes['date']) === false) {
            throw new AttributeValueException('Invalid value. Expected date string', 'date', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }

        try {
            return new \DateTime($attributes['date']);
        } catch (\Exception $e) {
            throw new AttributeValueException('Invalid value. Expected date string', 'date', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }
    }

    private function isCustomCollection(ItemInterface $item)
    {
        return !in_array($item->getCollection(), ['posts', 'pages']);
    }

    private function getPermalinkAttribute(ItemInterface $item)
    {
        $attributes = $item->getAttributes();
        $permalink = isset($attributes['permalink']) ? $attributes['permalink'] : $this->defaultPermalink;

        if (is_string($permalink) === false) {
            throw new AttributeValueException('Invalid value. Expected string.', 'permalink', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }

        if (trim($permalink) === '') {
            throw new AttributeValueException('Invalid value. Expected a non-empty value.', 'permalink', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }

        return $permalink;
    }

    private function sanitize($url)
    {
        $count = 0;

        if ((new StringWrapper($url))->startWith('/') === false) {
            $url = '/'.$url;
        }

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
