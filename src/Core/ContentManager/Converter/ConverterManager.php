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

use Yosymfony\Spress\Core\DataSource\ItemInterface;
use Yosymfony\Spress\Core\Support\StringWrapper;

/**
 * Converter manager.
 *
 * @api
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConverterManager
{
    private $queue;
    private $textExtensions;

    /**
     * Constructor.
     */
    public function __construct(array $textExtensions = [])
    {
        $this->textExtensions = $textExtensions;
        $this->initializeQueue();
    }

    /**
     * Adds a converter.
     *
     * @param Yosymfony\Spress\Core\ContentManager\Converter\ConverterInterface $converter The converter
     *
     * @throws RuntimeException If invalid priority at the converter
     */
    public function addConverter(ConverterInterface $converter)
    {
        $priority = $converter->getPriority();

        if (false === (is_int($priority) && $priority >= 0 && $priority <= 10)) {
            throw new \InvalidArgumentException(sprintf('Invalid priority at the converter: "%s".', get_class($converter)));
        }

        $this->queue->insert($converter, $priority);
    }

    /**
     * Clears all converters registered.
     */
    public function clearConverter()
    {
        $this->initializeQueue();
    }

    /**
     * Counts the converters registered.
     *
     * @return int
     */
    public function countConverter()
    {
        return $this->queue->count();
    }

    /**
     * Converts the content.
     *
     * @param string $content        The content
     * @param string $inputExtension The filename extension. e.g: 'html'
     *
     * @return Yosymfony\Spress\Core\ContentManager\Converter\ConverterResult
     *
     * @throws RuntimeException If there's no converter for the extension passed
     */
    public function convertContent($content, $inputExtension)
    {
        $converter = $this->getConverter($inputExtension);
        $content = $converter->convert($content);
        $outputExtension = $converter->getOutExtension($inputExtension);

        return new ConverterResult(
            $content,
            $inputExtension,
            $outputExtension
        );
    }

    /**
     * Converts an item. This method uses the SNAPSHOT_PATH_RELATIVE of Item path.
     *
     * @param Yosymfony\Spress\Core\DataSource\ItemInterface $item The item
     *
     * @return Yosymfony\Spress\Core\ContentManager\Converter\ConverterResult
     *
     * @throws RuntimeException If there's no converter for the extension passed
     */
    public function convertItem(ItemInterface $item)
    {
        $path = $item->getPath(ItemInterface::SNAPSHOT_PATH_RELATIVE);
        $str = new StringWrapper($path);
        $extension = $str->getFirstEndMatch($this->textExtensions);

        if ($extension === '') {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
        }

        return $this->convertContent($item->getContent(), $extension);
    }

    private function getConverter($extension)
    {
        $queue = clone $this->queue;

        foreach ($queue as $converter) {
            if ($converter->matches($extension)) {
                return $converter;
            }
        }

        throw new \RuntimeException(sprintf('There\'s no converter for the extension: "%s".', $extension));
    }

    private function initializeQueue()
    {
        $this->queue = new \SplPriorityQueue();
        $this->queue->setExtractFlags(\SplPriorityQueue::EXTR_DATA);
    }
}
