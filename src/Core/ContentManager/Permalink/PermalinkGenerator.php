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
     * Get a permalink.
     *
     * Item's attributes with special meaning:
     *  - title
     *  - date
     *  - categories
     *  - permalink
     *
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface $item
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkInterface
     */
    public function getPermalink(ItemInterface $item)
    {
        $placeholders = $this->getPlacehoders($item);

        $urlTemplate = '/:path/:basename.:extension';
        $pathTemplate = $urlTemplate;

        switch ($this->getPermalinkAttribute($item)) {
            case 'ordinal':
                break;
            case 'date':
                break;
            case 'pretty':
                if ($placeholders[':extension'] !== 'html') {
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

                break;
        }

        $path = $this->generatePath($pathTemplate, $placeholders);
        $urlPath = $this->generateUrlPath($urlTemplate, $placeholders);

        return new Permalink($path, $urlPath);
    }

    private function getPlacehoders(ItemInterface $item)
    {
        $fileInfo = new \SplFileInfo($item->getPath());
        $time = $this->getDate($item);

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

        if (isset($attributes['title']) === false) {
            return;
        }

        return Utils::slugify($attributes['title']);
    }

    private function getDate(ItemInterface $item)
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
}
