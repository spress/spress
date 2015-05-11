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

use Yosymfony\Spress\Core\ContentManager\Collection\CollectionManager;
use Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkGenerator;
use Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface;
use Yosymfony\Spress\Core\DataSource\DataSourceManager;
use Yosymfony\Spress\Core\DataWriter\DataWriterInterface;
use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\Core\Plugin\PluginManager;

/**
 * Content manager
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ContentManager
{
    private $dataSourceManager;
    private $dataWriter;
    private $converterManager;
    private $CollectionManager;
    private $permalinkGenerator;
    private $renderizer;
    private $pluginManager;
    private $io;
    private $events;
    private $params;
    private $parserResult;

    /**
     * Constructor
     *
     * @param DataSourceManager $dataSourceManager
     * @param DataWriterInterface $dataWriter
     * @param ConverterManager $converterManager
     * @param CollectionManager $CollectionManager
     * @param PermalinkGenerator $permalinkGenerator
     * @param RenderizerInterface $renderizer
     * @param PluginManager $pluginManager
     * @param IOInterface $io
     */
    public function __construct(
        DataSourceManager $dataSourceManager,
        DataWriterInterface $dataWriter,
        ConverterManager $converterManager,
        CollectionManager $CollectionManager,
        PermalinkGenerator $permalinkGenerator,
        RenderizerInterface $renderizer,
        PluginManager $pluginManager,
        IOInterface $io)
    {
        $this->dataSourceManager = $dataSourceManager;
        $this->dataWriter = $dataWriter;
        $this->converterManager = $converterManager;
        $this->CollectionManager = $CollectionManager;
        $this->permalinkGenerator = $permalinkGenerator;
        $this->renderizer = $renderizer;
        $this->pluginManager = $pluginManager;
        $this->io = $io;
        $this->events = $this->pluginManager->getDispatcherShortcut();

        $this->params = [];
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Parse a site
     *
     * @return array Information about process
     */
    public function parseSite()
    {
        $this->reset();
        $this->setUp();
        $this->finish();

        return $this->dataResult;
    }

    private function reset()
    {
        $this->parserResult = [
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
        $this->dataWriter->setUp();
    }

    private function finish()
    {
        $this->events->dispatchFinish($this->dataResult);
        $this->dataWriter->tearDown();
    }
}
