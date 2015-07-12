<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Yosymfony\Spress\Core\ContentManager\Collection\CollectionManager;
use Yosymfony\Spress\Core\ContentManager\Collection\CollectionInterface;
use Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager;
use Yosymfony\Spress\Core\ContentManager\Generator\GeneratorManager;
use Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkGeneratorInterface;
use Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface;
use Yosymfony\Spress\Core\ContentManager\SiteAttribute\SiteAttributeInterface;
use Yosymfony\Spress\Core\DataSource\DataSourceManager;
use Yosymfony\Spress\Core\DataWriter\DataWriterInterface;
use Yosymfony\Spress\Core\DataSource\ItemInterface;
use Yosymfony\Spress\Core\Exception\AttributeValueException;
use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\Core\Plugin\PluginManager;

/**
 * Content manager.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ContentManager
{
    private $dataSourceManager;
    private $dataWriter;
    private $generatorManager;
    private $converterManager;
    private $CollectionManager;
    private $permalinkGenerator;
    private $siteAttribute;
    private $renderizer;
    private $pluginManager;
    private $eventDispatcher;
    private $io;
    private $timezone;
    private $safe;
    private $processDraft;

    private $attributes;
    private $spressAttributes;
    private $parseResult;

    private $items;

    /**
     * Constructor.
     *
     * @param Yosymfony\Spress\Core\DataSource\DataSourceManager                         $dataSourceManager
     * @param Yosymfony\Spress\Core\DataWriter\DataWriterInterface                       $dataWriter
     * @param Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager            $converterManager
     * @param Yosymfony\Spress\Core\ContentManager\Collection\CollectionManager          $CollectionManager
     * @param Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkGeneratorInterface $permalinkGenerator
     * @param Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface        $renderizer
     * @param Yosymfony\Spress\Core\ContentManager\SiteAttribute\SiteAttributeInterface  $siteAttribute
     * @param Yosymfony\Spress\Core\Plugin\PluginManager                                 $pluginManager
     * @param Symfony\Component\EventDispatcher\EventDispatcher                          $eventDispatcher
     */
    public function __construct(
        DataSourceManager $dataSourceManager,
        DataWriterInterface $dataWriter,
        GeneratorManager $generatorManager,
        ConverterManager $converterManager,
        CollectionManager $CollectionManager,
        PermalinkGeneratorInterface $permalinkGenerator,
        RenderizerInterface $renderizer,
        SiteAttributeInterface $siteAttribute,
        PluginManager $pluginManager,
        EventDispatcher $eventDispatcher,
        IOInterface $io
        ) {
        $this->dataSourceManager = $dataSourceManager;
        $this->dataWriter = $dataWriter;
        $this->generatorManager = $generatorManager;
        $this->converterManager = $converterManager;
        $this->CollectionManager = $CollectionManager;
        $this->permalinkGenerator = $permalinkGenerator;
        $this->renderizer = $renderizer;
        $this->siteAttribute = $siteAttribute;
        $this->pluginManager = $pluginManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->io = $io;

        $this->attributes = [];
        $this->spressAttributes = [];

        $this->items = [];

        $this->parseResult = [];
    }

    /**
     * Parse a site.
     *
     * @param array  $attributes
     * @param array  $spressAttributes
     * @param bool   $draft            Include draft posts.
     * @param bool   $safe             True for disabling custom plugins.
     * @param string $timezone         Sets the time zone. @see http://php.net/manual/en/timezones.php More time zones.
     *
     * @return array Information about process
     */
    public function parseSite(array $attributes, array $spressAttributes, $draft = false, $safe = false, $timezone = 'UTC')
    {
        $this->attributes = $attributes;
        $this->spressAttributes = $spressAttributes;
        $this->safe = $safe;
        $this->timezone = $timezone;
        $this->processDraft = $draft;

        $this->reset();
        $this->setUp();
        $this->InitializePlugins();
        $this->process();
        $this->finish();

        return $this->parseResult;
    }

    private function reset()
    {
        $this->items = [];
        $this->siteAttribute->initialize($this->attributes);
        $this->renderizer->clear();

        $this->parseResult = [
            'total_post' => 0,
            'processed_post' => 0,
            'drafts_post' => 0,
            'total_pages' => 0,
            'processed_pages' => 0,
            'other_resources' => 0,
        ];
    }

    private function setUp()
    {
        $this->configureTimezone($this->timezone);

        $this->siteAttribute->setAttribute('spress', $this->spressAttributes);
        $this->siteAttribute->setAttribute('site.drafts', $this->processDraft);
        $this->siteAttribute->setAttribute('site.safe', $this->safe);
        $this->siteAttribute->setAttribute('site.timezone', $this->timezone);

        $this->dataWriter->setUp();
    }

    private function InitializePlugins()
    {
        if ($this->safe === false) {
            $this->pluginManager->callInitialize();
        }
    }

    private function process()
    {
        $itemsGenerator = [];

        $this->dataSourceManager->load();

        $items = $this->dataSourceManager->getItems();

        foreach ($items as $item) {
            if ($this->isGenerator($item)) {
                $itemsGenerator[$item->getId()] = $item;

                continue;
            }

            $this->processCollection($item);
            $this->processDraftIfPost($item);
            $this->processOutputAttribute($item);

            $this->items[$item->getId()] = $item;
        }

        foreach ($itemsGenerator as $item) {
            $this->processGenerator($item);
        }

        $this->prepareRenderizer();

        foreach ($this->items as $item) {
            $this->convertItem($item);
            $this->processPermalink($item);
            $this->renderBlocks($item);
        }

        foreach ($this->items as $item) {
            $this->renderPage($item);
            $this->dataWriter->write($item);
        }
    }

    private function finish()
    {
        $this->dataWriter->tearDown();
    }

    private function processCollection(ItemInterface $item)
    {
        $collection = $this->CollectionManager->getCollectionForItem($item);
        $collectionName = $collection->getName();
        $collectionNamePath = sprintf('site.collections.%s', $this->escapeDot($collectionName));

        $attributes = $item->getAttributes();
        $attributes['collection'] = $collectionName;

        $newAttributes = array_merge($collection->getAttributes(), $attributes);

        $item->setAttributes($newAttributes);
        $item->setCollection($collectionName);

        $this->siteAttribute->setAttribute($collectionNamePath, $this->getCollectionAttributes($collection));
        $this->siteAttribute->setItem($item);
    }

    private function processDraftIfPost(ItemInterface $item)
    {
        $attributes = array_replace(['draft' => false], $item->getAttributes());

        if (is_bool($attributes['draft']) === false) {
            throw new AttributeValueException('Invalid value. Expected boolean.', 'draft', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }

        if ($item->getCollection() === 'posts' &&  $attributes['draft'] === true) {
            if ($this->processDraft === false) {
                $attributes['output'] = false;

                $item->setAttributes($attributes);
            }
        }
    }

    private function processOutputAttribute(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        if (array_key_exists('output', $attributes)) {
            $output = $attributes['output'];

            if (is_bool($output) === false) {
                throw new AttributeValueException('Invalid value. Expected boolean.', 'output', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
            }

            if ($output === false) {
                $item->setPath('', ItemInterface::SNAPSHOT_PATH_RELATIVE);
                $item->setPath('', ItemInterface::SNAPSHOT_PATH_SOURCE);
            }
        }
    }

    private function processGenerator(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        $generator = $this->generatorManager->getGenerator($attributes['generator']);
        $items = $generator->generateItems($item, $this->siteAttribute->getAttributes());

        foreach ($items as $item) {
            if (array_key_exists($item->getId(), $this->items) === true) {
                throw new \RuntimeException(sprintf('A previous item exists with the same id: "%s". Generator: "%s".', $id, $attributes['generator']));
            }

            $this->processCollection($item);
            $this->items[$item->getId()] = $item;
        }
    }

    private function isGenerator(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        return isset($attributes['generator']);
    }

    private function prepareRenderizer()
    {
        $layouts = $this->dataSourceManager->getLayouts();
        $includes = $this->dataSourceManager->getIncludes();

        foreach ($layouts as $item) {
            $this->renderizer->addLayout($item->getId(), $item->getContent(), $item->getAttributes());
        }

        foreach ($includes as $item) {
            $this->renderizer->addInclude($item->getId(), $item->getContent(), $item->getAttributes());
        }
    }

    private function convertItem(ItemInterface $item)
    {
        $path = $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE);
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $result = $this->converterManager->convertContent($item->getContent(), $ext);
        $newPath = preg_replace('/\.'.$ext.'$/', '.'.$result->getExtension(), $path);

        $item->setContent($result->getResult(), ItemInterface::SNAPSHOT_AFTER_CONVERT);
        $item->setPath($newPath, ItemInterface::SNAPSHOT_PATH_RELATIVE);

        $this->siteAttribute->setItem($item);
    }

    private function processPermalink(ItemInterface $item)
    {
        $permalink = $this->permalinkGenerator->getPermalink($item);

        $item->setPath($permalink->getPath(), ItemInterface::SNAPSHOT_PATH_PERMALINK);

        $attributes = $item->getAttributes();
        $attributes['url'] = $permalink->getUrlPath();

        $item->setAttributes($attributes);
    }

    private function renderBlocks(ItemInterface $item)
    {
        $snapshotRender = $this->renderizer->renderBlocks($item->getId(), $item->getContent(), $this->siteAttribute->getAttributes());

        $item->setContent($snapshotRender, ItemInterface::SNAPSHOT_AFTER_RENDER_BLOCKS);

        $this->siteAttribute->setItem($item);
    }

    private function renderPage(ItemInterface $item)
    {
        $layout = $this->getLayoutAttribute($item);

        $snapshotPage = $this->renderizer->renderPage($item->getId(), $item->getContent(), $layout, $this->siteAttribute->getAttributes());

        $item->setContent($snapshotPage, ItemInterface::SNAPSHOT_AFTER_PAGE);

        $this->siteAttribute->setItem($item);
    }

    private function getCollectionAttributes(CollectionInterface $collection)
    {
        $result = $collection->getAttributes();

        $result['path'] = $collection->getPath();

        return $result;
    }

    private function configureTimezone($timezone)
    {
        if (is_string($timezone) === false) {
            throw new \RuntimeException('Invalid timezone. Expected a string value.');
        }

        if (strlen($timezone) == 0) {
            throw new \RuntimeException('Invalid timezone. Expected a non-empty value.');
        }

        date_default_timezone_set($timezone);
    }

    private function getLayoutAttribute(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        if (array_key_exists('layout', $attributes) === false) {
            return;
        }

        if (is_string($attributes['layout']) === false) {
            throw new AttributeValueException('Invalid value. Expected string.', 'layout', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }

        if (strlen($attributes['layout']) == 0) {
            throw new AttributeValueException('Invalid value. Expected a non-empty string.', 'layout', $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE));
        }

        return $attributes['layout'];
    }

    private function escapeDot($key)
    {
        return str_replace('.', '[.]', $key);
    }
}
