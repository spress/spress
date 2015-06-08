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
use Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkGenerator;
use Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface;
use Yosymfony\Spress\Core\DataSource\DataSourceManager;
use Yosymfony\Spress\Core\DataWriter\DataWriterInterface;
use Yosymfony\Spress\Core\DataSource\ItemInterface;
use Yosymfony\Spress\Core\Exception\AttributeValueException;
use Yosymfony\Spress\Core\IO\IOInterface;

/**
 * Content manager
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
    private $renderizer;
    private $pluginManager;
    private $io;
    private $eventDispatcher;
    private $timezone;
    private $safe;

    private $attributes;
    private $siteAttributes;
    private $spressAttributes;
    private $parseResult;

    private $items;
    private $itemsGenerator;

    /**
     * Constructor
     *
     * @param Yosymfony\Spress\Core\DataSource\DataSourceManager                  $dataSourceManager
     * @param Yosymfony\Spress\Core\DataWriter\DataWriterInterface                $dataWriter
     * @param Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager     $converterManager
     * @param Yosymfony\Spress\Core\ContentManager\Collection\CollectionManager   $CollectionManager
     * @param Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkGenerator   $permalinkGenerator
     * @param Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface $renderizer
     * @param Symfony\Component\EventDispatcher\EventDispatcher                   $eventDispatcher
     * @param Yosymfony\Spress\Core\IO\IOInterface                                $io
     */
    public function __construct(
        DataSourceManager $dataSourceManager,
        DataWriterInterface $dataWriter,
        GeneratorManager $generatorManager,
        ConverterManager $converterManager,
        CollectionManager $CollectionManager,
        PermalinkGenerator $permalinkGenerator,
        RenderizerInterface $renderizer,
        EventDispatcher $eventDispatcher,
        IOInterface $io)
    {
        $this->dataSourceManager = $dataSourceManager;
        $this->dataWriter = $dataWriter;
        $this->generatorManager = $generatorManager;
        $this->converterManager = $converterManager;
        $this->CollectionManager = $CollectionManager;
        $this->permalinkGenerator = $permalinkGenerator;
        $this->renderizer = $renderizer;
        $this->eventDispatcher = $eventDispatcher;
        $this->io = $io;

        $this->attributes = [];
        $this->siteAttributes = [];
        $this->spressAttributes = [];

        $this->items = [];
        $this->itemsGenerator = [];

        $this->parseResult = [];
    }

    /**
     * Parse a site
     *
     * @param array  $attributes
     * @param array  $spressAttributes
     * @param bool   $safe             True for disabling custom plugins.
     * @param string $timezone         Sets the time zone. @see http://php.net/manual/en/timezones.php More time zones.
     *
     * @return array Information about process
     */
    public function parseSite(array $attributes, array $spressAttributes, $safe = false, $timezone = 'UTC')
    {
        $this->attributes = $attributes;
        $this->spressAttributes = $spressAttributes;
        $this->safe = $safe;
        $this->timezone = $timezone;

        $this->reset();
        $this->setUp();
        $this->process();
        $this->finish();

        return $this->parseResult;
    }

    private function reset()
    {
        $this->items = [];
        $this->itemsGenerator = [];
        $this->siteAttributes = [];

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
        $this->generateSiteAttributes();
        $this->dataWriter->setUp();
    }

    private function process()
    {
        $this->dataSourceManager->load();

        $items = $this->dataSourceManager->getItems();

        foreach ($items as $item) {
            if ($this->isGenerator($item)) {
                $this->itemsGenerator[$item->getId()] = $item;

                continue;
            }

            $this->processCollection($item);

            $this->items[$item->getId()] = $item;
        }

        foreach ($this->itemsGenerator as $item) {
            $this->processGenerator($item);
        }

        $this->prepareRenderizer();

        foreach ($this->items as $item) {
            $this->convertItem($item);
            $this->processPermalink($item);

            $snapshotRender = $this->renderBlocks($item);
            $item->setContent($snapshotRender, ItemInterface::SNAPSHOT_AFTER_RENDER_BLOCKS);
        }

        foreach ($this->items as $item) {
            $snapshotPage = $this->renderPage($item);

            $item->setContent($snapshotPage, ItemInterface::SNAPSHOT_AFTER_PAGE);

            $this->dataWriter->write($item);
        }
    }

    private function finish()
    {
        $this->dataWriter->tearDown();
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

    private function renderBlocks(ItemInterface $item)
    {
        return $this->renderizer->renderBlocks($item->getId(), $item->getContent(), $this->siteAttributes);
    }

    private function renderPage(ItemInterface $item)
    {
        $layout = $this->getLayoutAttribute($item);
        $this->siteAttributes['page'] = $this->getItemAttributes($item);

        return $this->renderizer->renderPage($item->getId(), $item->getContent(), $layout, $this->siteAttributes);
    }

    private function processCollection(ItemInterface $item)
    {
        $collection = $this->CollectionManager->getCollectionForItem($item);
        $collectionName = $collection->getName();

        $attributes = $item->getAttributes();
        $attributes['collection'] = $collectionName;

        $newAttributes = array_merge($attributes, $collection->getAttributes());

        $item->setAttributes($newAttributes);

        if (array_key_exists($collectionName, $this->siteAttributes['site']) === false) {
            $this->siteAttributes['site'][$collectionName] = [];
            $this->siteAttributes['site']['collections'][$collectionName] = $this->getCollectionAttributes($collection);
        }

        $this->siteAttributes['site'][$collectionName][$item->getId()] = $this->getItemAttributes($item);
    }

    private function isGenerator(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        return isset($attributes['generator']);
    }

    private function processGenerator(ItemInterface $item)
    {
        $attributes = $item->getAttributes();

        $generator = $this->generatorManager->getGenerator($attributes['generator']);
        $items = $generator->generateItems($item, $this->siteAttributes);

        foreach ($items as $item) {
            if (array_key_exists($item->getId(), $this->items) === true) {
                throw new \RuntimeException(sprintf('A previous item exists with the same id: "%s". Generator: "%s".', $id, $attributes['generator']));
            }

            $this->processCollection($item);
            $this->items[$item->getId()] = $item;
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
    }

    private function processPermalink(ItemInterface $item)
    {
        $permalink = $this->permalinkGenerator->getPermalink($item);

        $item->setPath($permalink->getPath(), ItemInterface::SNAPSHOT_PATH_PERMALINK);

        $attributes = $item->getAttributes();
        $attributes['url'] = $permalink->getUrlPath();

        $item->setAttributes($attributes);
    }

    private function generateSiteAttributes()
    {
        $this->siteAttributes['spress']  = $this->spressAttributes;
        $this->siteAttributes['site'] = $this->attributes;
        $this->siteAttributes['site']['time'] = new \DateTime('now');
        $this->siteAttributes['site']['safe'] = $this->safe;
        $this->siteAttributes['site']['timezone'] = $this->timezone;
        $this->siteAttributes['site']['collections'] = [];
        $this->siteAttributes['site']['categories'] = [];
        $this->siteAttributes['site']['tags'] = [];
        $this->siteAttributes['page'] = [];
    }

    private function getItemAttributes(ItemInterface $item)
    {
        $result = $item->getAttributes();

        $result['id'] = $item->getId();
        $result['content'] = $item->getContent();
        $result['path'] = $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE);

        return $result;
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
}
