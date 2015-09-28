<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Generator\Pagination;

use Yosymfony\Spress\Core\ContentManager\Generator\GeneratorInterface;
use Yosymfony\Spress\Core\DataSource\ItemInterface;
use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\Support\ArrayWrapper;
use Yosymfony\Spress\Core\Support\AttributesResolver;
use Yosymfony\Spress\Core\Exception\AttributeValueException;

/**
 * Pagination generator lets you generate multiples
 * pages around a set of items.
 *
 * Example of URLs generated:
 *  /
 *  /page2
 *  ...
 *
 * How to configure? (frontmatter of the template page):
 *
 * ---
 * layout: default
 * generator: pagination
 * max_page: 5
 * provider: 'site.posts'
 * permalink: '/page:num'
 * ---
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PaginationGenerator implements GeneratorInterface
{
    /**
     * @inheritDoc
     *
     * @throws Yosymfony\Spress\Core\Exception\AttributeValueException if bad attribute value.
     */
    public function generateItems(ItemInterface $templateItem, array $collections)
    {
        $result = [];

        $options = $this->getAttributesResolver($templateItem);

        if ($options['max_page'] < 1) {
            throw new AttributeValueException('Items per page value must be great than 0.', 'max_page', $templateItem->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }

        $arr = new ArrayWrapper($collections);
        $providerName = $this->providerToCollection($options['provider']);

        if ($arr->has($providerName) === false || is_array($provider = $arr->get($providerName)) === false) {
            throw new AttributeValueException(
                sprintf('Provider: "%s" for pagination not found.', $options['provider']),
                'provider',
                $templateItem->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }

        $arr->setArray($provider);

        $pages = $arr->paginate($options['max_page']);

        $totalPages = count($pages);
        $totalItems = count($provider);
        $templatePath = dirname($templateItem->getPath(Item::SNAPSHOT_PATH_RELATIVE));

        if ($templatePath === '.') {
            $templatePath = '';
        }

        foreach ($pages as $page => $items) {
            $previousPage = $page > 1 ? $page - 1 : null;
            $previousPagePath = $this->getPageRelativePath($templatePath, $options['permalink'], $previousPage);
            $previousPageUrl = $this->getPagePermalink($previousPagePath);
            $nextPage = $page === $totalPages ? null : $page + 1;
            $nextPagePath = $this->getPageRelativePath($templatePath, $options['permalink'], $nextPage);
            $nextPageUrl = $this->getPagePermalink($nextPagePath);

            $pageAttr = new ArrayWrapper($templateItem->getAttributes());
            $pageAttr->set('pagination.per_page', $options['max_page']);
            $pageAttr->set('pagination.total_items', $totalItems);
            $pageAttr->set('pagination.total_pages', $totalPages);
            $pageAttr->set('pagination.page', $page);
            $pageAttr->set('pagination.previous_page', $previousPage);
            $pageAttr->set('pagination.previous_page_path', $previousPagePath);
            $pageAttr->set('pagination.previous_page_url', $previousPageUrl);
            $pageAttr->set('pagination.next_page', $nextPage);
            $pageAttr->set('pagination.next_page_path', $nextPagePath);
            $pageAttr->set('pagination.next_page_url', $nextPageUrl);

            $pageAttr->remove('permalink');

            $pagePath = $this->getPageRelativePath($templatePath, $options['permalink'], $page);
            $permalink = $this->getPagePermalink($pagePath);

            $item = new PaginationItem($templateItem->getContent(), $pagePath, $pageAttr->getArray());
            $item->setPageItems($items);
            $item->setPath($pagePath, Item::SNAPSHOT_PATH_RELATIVE);
            $item->setPath($permalink, Item::SNAPSHOT_PATH_PERMALINK);

            $result[] = $item;
        }

        return $result;
    }

    protected function getPageRelativePath($basePath, $template, $page)
    {
        $result = $basePath;

        if (is_null($page)) {
            return;
        }

        $pagePath = str_replace(':num', $page, $template);
        $basename = basename($pagePath);

        if (preg_match('/^(.+?)\.(.+)$/', $basename)) {
            $result .= '/'.($page > 1 ? $pagePath : $basename);
        } else {
            if ($page > 1) {
                $result .= '/'.$pagePath;
            }

            $result .= '/index.html';
        }

        return ltrim(preg_replace('/\/\/+/', '/', $result), '/');
    }

    protected function getPagePermalink($pageRelativePath)
    {
        if (is_null($pageRelativePath)) {
            return;
        }

        $result = $pageRelativePath;
        $basename = basename($pageRelativePath);

        if ($basename === 'index.html') {
            $result = dirname($pageRelativePath);

            if ($result === '.') {
                $result = '';
            }
        }

        return '/'.$result;
    }

    protected function getAttributesResolver(ItemInterface $templateItem)
    {
        $resolver = new AttributesResolver();
        $resolver->setDefault('max_page', 5, 'int')
            ->setDefault('provider', 'site.posts', 'string')
            ->setDefault('permalink', '/page:num');

        $attributes = $templateItem->getAttributes();

        return $resolver->resolve($attributes);
    }

    protected function providerToCollection($providerName)
    {
        return str_replace('site.', '', $providerName);
    }
}
