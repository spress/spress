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

/**
 * Converter manager.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConverterManager
{
    private $queue;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->queue = new \SplPriorityQueue();
        $this->queue->setExtractFlags(\SplPriorityQueue::EXTR_DATA);
    }

    /**
     * Adds a converter.
     *
     * @param \Yosymfony\Spress\Core\ContentManager\Converter\ConverterInterface $converter The converter.
     *
     * @throws RuntimeException If invalid priority at the converter.
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
     * Converts the content.
     *
     * @param string $content
     * @param string $extension
     *
     * @return \Yosymfony\Spress\Core\ContentManager\Converter\ConverterResult
     *
     * @throws RuntimeException If there's no converter for the extension passed.
     */
    public function convertContent($content, $extension)
    {
        $converter = $this->getConverter($extension);
        $content = $converter->convert($content);
        $outExtension = $converter->getOutExtension($extension);

        return new ConverterResult(
            $content,
            $outExtension
        );
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
}
