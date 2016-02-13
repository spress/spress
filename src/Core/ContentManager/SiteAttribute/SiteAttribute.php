<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\SiteAttribute;

use Yosymfony\Spress\Core\DataSource\ItemInterface;
use Yosymfony\Spress\Core\Support\ArrayWrapper;
use Yosymfony\Spress\Core\Support\AttributesResolver;

/**
 * The site's attribute structure.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SiteAttribute implements SiteAttributeInterface
{
    protected $arrayWrapper;
    protected $postAttributesResolver;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->arrayWrapper = new ArrayWrapper();
        $this->postAttributesResolver = $this->getPostAttributesResolver();

        $this->initialize();
    }

    /**
     * {@inheritdoc}
     */
    public function addAttribute($name, $value)
    {
        $this->arrayWrapper->add($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        return $this->arrayWrapper->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->arrayWrapper->getArray();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute($name)
    {
        return $this->arrayWrapper->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($name, $value)
    {
        $this->arrayWrapper->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setItem(ItemInterface $item)
    {
        $previousAttributes = [];
        $attributes = $this->getItemAttributes($item);

        $id = str_replace('.', '[.]', $item->getId());
        $collectionName = $item->getCollection();

        $itemPathName = sprintf('site.%s.%s', $collectionName, $id);

        if ($this->hasAttribute($itemPathName)) {
            $previousAttributes = $this->getAttribute($itemPathName);
        }

        $newAttributes = array_merge($previousAttributes, $attributes);

        $this->setAttribute($itemPathName, $newAttributes);
        $this->setAttribute('page', $newAttributes);

        if ($collectionName === 'posts') {
            $postAttributes = $this->postAttributesResolver->resolve($attributes);

            foreach ($postAttributes['categories'] as $category) {
                $this->setAttribute(sprintf('site.categories.%s.%s', $category, $id), $newAttributes);
            }

            foreach ($postAttributes['tags'] as $tag) {
                $this->setAttribute(sprintf('site.tags.%s.%s', $tag, $id), $newAttributes);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array $attributes = [])
    {
        $site = [];
        $site['spress'] = [];
        $site['site'] = $attributes;
        $site['site']['time'] = new \DateTime('now');
        $site['site']['collections'] = [];
        $site['site']['categories'] = [];
        $site['site']['tags'] = [];
        $site['page'] = [];

        $this->arrayWrapper->setArray($site);
    }

    protected function getItemAttributes(ItemInterface $item)
    {
        $result = $item->getAttributes();

        $result['id'] = $item->getId();
        $result['content'] = $item->getContent();
        $result['collection'] = $item->getCollection();
        $result['path'] = $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE);
        $result['relationships'] = [];

        $relationships = $item->getRelationshipCollection();

        foreach ($relationships as $name => $items) {
            if (isset($result['relationships'][$name]) === false) {
                $result['relationships'][$name] = [];
            }

            foreach ($items as $relItem) {
                $relAttributes = $relItem->getAttributes();
                $relAttributes['id'] = $relItem->getId();
                $relAttributes['content'] = $relItem->getContent();
                $relAttributes['collection'] = $relItem->getCollection();
                $relAttributes['path'] = $relItem->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE);

                $result['relationships'][$name][] = $relAttributes;
            }
        }

        return $result;
    }

    protected function getPostAttributesResolver()
    {
        $resolver = new AttributesResolver();
        $resolver->setDefault('categories', [], 'array')
            ->setDefault('tags', [], 'array');

        return $resolver;
    }
}
