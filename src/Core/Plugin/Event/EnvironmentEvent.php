<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Plugin\Event;

use Symfony\Component\EventDispatcher\Event;
use Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface;
use Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager;
use Yosymfony\Spress\Core\DataSource\DataSourceManager;
use Yosymfony\Spress\Core\DataWriter\DataWriterInterface;
use Yosymfony\Spress\Core\IO\IOInterface;

/**
 * Environment event.
 *
 * Used with events:
 *   - "spress.start".
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class EnvironmentEvent extends Event
{
    protected $dataSourceManager;
    protected $converterManager;
    protected $dataWriter;
    protected $renderizer;
    protected $io;

    public function __construct(
        DataSourceManager $dataSourceManager,
        DataWriterInterface $dataWriter,
        ConverterManager $converterManager,
        RenderizerInterface $renderizer,
        IOInterface $io,
        array &$configValues)
    {
        $this->dataSourceManager = $dataSourceManager;
        $this->converterManager = $converterManager;
        $this->dataWriter = $dataWriter;
        $this->renderizer = $renderizer;
        $this->io = $io;
        $this->configValues = &$configValues;
    }

    /**
     * Gets the data source manager.
     *
     * @return \Yosymfony\Spress\Core\DataSource\DataSourceManager
     */
    public function getDataSourceManager()
    {
        return $this->dataSourceManager;
    }

    /**
     * Gets the converter manager.
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager
     */
    public function getConverterManager()
    {
        return $this->converterManager;
    }

    /**
     * Gets the renderizer.
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface
     */
    public function getRenderizer()
    {
        return $this->renderizer;
    }

    /**
     * Sets a custom renderizer.
     *
     * @param \Yosymfony\Spress\Core\ContentManager\Renderizer\RenderizerInterface $renderizer The renderizer.
     */
    public function setRenderizer(RenderizerInterface $renderizer)
    {
        $this->renderizer = $renderizer;
    }

    /**
     * Gets the data writer.
     *
     * @return \Yosymfony\Spress\Core\DataWriter\DataWriterInterface
     */
    public function getDataWriter()
    {
        return $this->dataWriter;
    }

    /**
     * Sets the data writer.
     *
     * @param \Yosymfony\Spress\Core\DataWriter\DataWriterInterface $dataWriter
     */
    public function setDataWriter(DataWriterInterface $dataWriter)
    {
        $this->dataWriter = $dataWriter;
    }

    /**
     * Gets IO.
     *
     * @return \Yosymfony\Spress\IO\IOInterface
     */
    public function getIO()
    {
        return $this->io;
    }

    /**
     * Gets the configuration values.
     *
     * @return array
     */
    public function getConfigValues()
    {
        return $this->configValues;
    }

    /**
     * Sets the configuration values.
     *
     * @param array $values
     */
    public function setConfigValues(array $values)
    {
        $this->configValues = $values;
    }
}
