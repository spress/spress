<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Converter;

use Yosymfony\Spress\Core\Configuration;

/**
 * Converter manager
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConverterManager
{
    private $queue;
    private $converters;
    private $configuration;
    private $extension = [];

    /**
     * Constructor
     *
     * @param Configuration $configuration
     * @param array         $converters    Array of ConverterInterface objects
     */
    public function __construct(Configuration $configuration, array $converters = [])
    {
        $this->configuration = $configuration;

        foreach ($converters as $converter) {
            $this->addConverter($converter);
        }
    }

    /**
     * Initialize life cycle.
     * Generate the converters priority queue.
     */
    public function initialize()
    {
        $this->queue = new \SplPriorityQueue();
        $this->queue->setExtractFlags(\SplPriorityQueue::EXTR_DATA);
        $extension = [];

        foreach ($this->converters as $converter) {
            $converter->initialize($this->configuration->getRepository()->getArray());
            $priority = $converter->getPriority();

            $this->queue->insert($converter, $priority);

            $extension = array_merge($extension, $converter->getSupportExtension());
        }

        $this->extension = array_unique($extension);
    }

    /**
     * Get support file extensions by converters
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extension;
    }

    /**
     * Add new converter
     *
     * @param ConverterInterface $converter
     */
    public function addConverter(ConverterInterface $converter)
    {
        $priority = $converter->getPriority();

        if (false === (is_int($priority) && $priority >= 0 && $priority <= 10)) {
            throw new \InvalidArgumentException(sprintf('Invalid priority at the converter %s', get_class($converter)));
        }

        $this->converters[] = $converter;
    }

    /**
     * Convert item
     *
     * @param ContentItemInterface $item
     *
     * @return ContentItemInterface
     */
    public function convertItem(ContentItemInterface $item)
    {
        $extension = $item->getFileItem()->getExtension();
        $converter = $this->getConverter($extension);

        $outExtension = $converter->getOutExtension($extension);
        $content = $converter->convert($item->getPreConverterContent());

        $item->setPostConverterContent($content);
        $item->setOutExtension($outExtension);

        return new ConverterResult(
            $content,
            $outExtension,
            get_class($converter)
        );
    }

    /**
     * @return ConverterInterface
     */
    private function getConverter($extension)
    {
        $queue = clone $this->queue;

        foreach ($queue as $converter) {
            if ($converter->matches($extension)) {
                return $converter;
            }
        }

        throw new \RuntimeException('No converter was found');
    }
}
