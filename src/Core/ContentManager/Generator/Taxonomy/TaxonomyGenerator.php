<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Generator\Taxonomy;

use Yosymfony\Spress\Core\ContentManager\Generator\GeneratorInterface;
use Yosymfony\Spress\Core\ContentManager\Generator\Pagination\PaginationGenerator;
use Yosymfony\Spress\Core\DataSource\ItemInterface;
use Yosymfony\Spress\Core\Support\ArrayWrapper;
use Yosymfony\Spress\Core\Support\AttributesResolver;
use Yosymfony\Spress\Core\Support\StringWrapper;

/**
 * Taxonomy generator lets you group items around a term.
 * This generator uses PaginationGenerator for generating
 * multiples pages for each term.
 *
 * Example of URLs generated:
 *  /categories/news
 *  /categories/news/page2
 *  ...
 *
 * This generator adds an attribute "terms_url" to each
 * items processed with the permalinks of the terms. The patter
 * follows is $attributes['terms_url'][$taxonomy_attribute][$term].
 *
 * e.g: for "categories" as taxonomy_attribute and "news" as term
 *  $attributes['terms_url']['categories']['news']
 *
 * How to configure? (frontmatter of the template page):
 *
 * ---
 * layout: default
 * generator: taxonomy
 * max_page: 5
 * taxonomy_attribute: 'categories'
 * permalink: '/:name'
 * pagination_permalink: '/page:num'
 * ---
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class TaxonomyGenerator implements GeneratorInterface
{
    /**
     * @inheritDoc
     *
     * @throws Yosymfony\Spress\Core\Exception\AttributeValueException if bad attribute value.
     */
    public function generateItems(ItemInterface $templateItem, array $collections)
    {
        $result = [];
        $taxonomyCollection = [];
        $templateAttributes = $templateItem->getAttributes();
        $options = $this->getAttributesResolver($templateItem);
        $taxonomyAttribute = $options['taxonomy_attribute'];
        $permalink = $options['permalink'];

        $templateAttributes['permalink'] = $options['pagination_permalink'];
        $templatePath = dirname($templateItem->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));

        $items = (new ArrayWrapper($collections))->flatten();

        foreach ($items as $item) {
            $attributes = $item->getAttributes();

            if (isset($attributes[$taxonomyAttribute]) === false) {
                continue;
            }

            $terms = (array) $attributes[$taxonomyAttribute];

            foreach ($terms as $term) {
                if (empty(trim($term)) === true) {
                    continue;
                }

                $taxonomyCollection[$term][] = $item;
            }
        }

        foreach ($taxonomyCollection as $term => $items) {
            $templateAttributes['provider'] = 'site.'.$term;
            $templateAttributes['term'] = $term;
            $templateItem->setAttributes($templateAttributes);
            $termPath = $this->getTermRelativePath($templatePath, $permalink, $term);
            $templateItem->setPath($termPath, ItemInterface::SNAPSHOT_PATH_RELATIVE);

            $paginationGenerator = new PaginationGenerator();
            $itemsGenerated = $paginationGenerator->generateItems($templateItem, [$term => $items]);

            $this->setTermsPermalink($items, $taxonomyAttribute, $term, $termPath);

            $result = array_merge($result, $itemsGenerated);
        }

        return $result;
    }

    protected function setTermsPermalink(array $items, $taxonomyAttribute, $term, $termRelativePath)
    {
        foreach ($items as $item) {
            $attributes = $item->getAttributes();

            if (isset($attributes['term_urls'])) {
                $attributes['term_urls'] = [];
            }

            if (isset($attributes['term_urls'][$taxonomyAttribute])) {
                $attributes['term_urls'][$taxonomyAttribute] = [];
            }

            $attributes['terms_url'][$taxonomyAttribute][$term] = $this->getTermPermalink($termRelativePath);

            $item->setAttributes($attributes);
        }
    }

    protected function getTermRelativePath($basePath, $permalinkTemplate, $term)
    {
        $result = $basePath;
        $slugedTerm = (new StringWrapper($term))->slug();
        $result .= '/'.str_replace(':name', $slugedTerm, $permalinkTemplate).'/index.html';

        return ltrim(preg_replace('/\/\/+/', '/', $result), '/');
    }

    protected function getTermPermalink($TermRelativePath)
    {
        if (is_null($TermRelativePath)) {
            return;
        }

        $result = $TermRelativePath;
        $basename = basename($TermRelativePath);

        if ($basename === 'index.html') {
            $result = dirname($TermRelativePath);

            if ($result === '.') {
                $result = '';
            }
        }

        return '/'.$result;
    }

    protected function getAttributesResolver(ItemInterface $templateItem)
    {
        $resolver = new AttributesResolver();
        $resolver->setDefault('taxonomy_attribute', 'categories', 'string')
            ->setDefault('permalink', '/:name')
            ->setDefault('pagination_permalink', '/page:num', 'string');

        $attributes = $templateItem->getAttributes();

        return $resolver->resolve($attributes);
    }
}
