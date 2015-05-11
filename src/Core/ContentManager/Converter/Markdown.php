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

use Michelf\MarkdownExtra;
use Yosymfony\Spress\Core\ContentManager\ConverterInterface;

/**
 * Markdown converter
 *
 * @author Victor Pueras <vpgugr@gmail.com>
 */
class Markdown implements ConverterInterface
{
    private $supportExtension = [];

    /**
     * Initialize the converter
     *
     * @param array $config Configuration parameters
     */
    public function initialize(array $config)
    {
        if (false === isset($config['markdown_ext'])) {
            throw new \InvalidArgumentException('markdown_ext key was not found in Markdown converter.');
        }

        $this->supportExtension = $config['markdown_ext'];
    }

    /**
     * Get the converter priority
     *
     * @return int
     */
    public function getPriority()
    {
        return 1;
    }

    /**
     * If file's extension is support by converter
     *
     * @param string $extension Extension without dot
     *
     * @return boolean
     */
    public function matches($extension)
    {
        return in_array($extension, $this->supportExtension);
    }

    /**
     * Convert the input data
     *
     * @param string $input The raw content without Front-matter
     *
     * @return string
     */
    public function convert($input)
    {
        if (!is_string($input)) {
            throw new \InvalidArgumentException('Expected Markdown string to parse');
        }

        return MarkdownExtra::defaultTransform($input);
    }

    /**
     * The extension of filename result (without dot). E.g: html.
     *
     * @return string
     */
    public function getOutExtension($extension)
    {
        return 'html';
    }
}
