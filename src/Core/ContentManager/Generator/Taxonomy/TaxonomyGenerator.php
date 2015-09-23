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

/**
 * Taxonomy generator. This generator uses PaginationGenerator for
 * generating multiples pages around each of taxon.
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

            $taxa = (array) $attributes[$taxonomyAttribute];

            foreach ($taxa as $taxon) {
                if (empty(trim($taxon)) === true) {
                    continue;
                }

                $taxonomyCollection[$taxon][] = $item;
            }
        }

        foreach ($taxonomyCollection as $taxon => $items) {
            $templateAttributes['provider'] = 'site.'.$taxon;
            $templateAttributes['taxon'] = $taxon;
            $templateItem->setAttributes($templateAttributes);
            $taxonPath = $this->getTaxonRelativePath($templatePath, $permalink, $taxon);
            $templateItem->setPath($taxonPath, ItemInterface::SNAPSHOT_PATH_RELATIVE);

            $paginationGenerator = new PaginationGenerator();
            $itemsGenerated = $paginationGenerator->generateItems($templateItem, [$taxon => $items]);

            $result = array_merge($result, $itemsGenerated);
        }

        return $result;
    }

    protected function getTaxonRelativePath($basePath, $permalinkTemplate, $taxon)
    {
        $result = $basePath;
        $result .= '/'.str_replace(':name', $taxon, $permalinkTemplate);

        return ltrim(preg_replace('/\/\/+/', '/', $result), '/');
    }

    protected function getAttributesResolver(ItemInterface $templateItem)
    {
        $resolver = new AttributesResolver();
        $resolver->setDefault('taxonomy_attribute', 'categories', 'string')
            ->setDefault('permalink', '/:name/page:num')
            ->setDefault('pagination_permalink', '/page:num', 'string');

        $attributes = $templateItem->getAttributes();

        return $resolver->resolve($attributes);
    }
}
