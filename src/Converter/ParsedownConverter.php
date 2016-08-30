<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Converter;

use Yosymfony\Spress\Core\ContentManager\Converter\ConverterInterface;

/**
 * Markdown converter implementation using a Parsedown (extra) parser by
 * Emanuil Rusev. See http://parsedown.org/ or https://github.com/erusev/parsedown-extra.
 *
 * @author Victor Pueras <vpgugr@gmail.com>
 */
class ParsedownConverter implements ConverterInterface
{
    private $supportedExtension;

    /**
     * Constructor.
     *
     * @param array $supportedExtension File extesion supported by the converter. Extension without dot
     */
    public function __construct(array $supportedExtension)
    {
        $this->supportedExtension = $supportedExtension;
    }

    /**
     * Get the converter priority.
     *
     * @return int
     */
    public function getPriority()
    {
        return 1;
    }

    /**
     * If file's extension is support by converter.
     *
     * @param string $extension Extension without dot
     *
     * @return bool
     */
    public function matches($extension)
    {
        return in_array($extension, $this->supportedExtension);
    }

    /**
     * Convert the input data.
     *
     * @param string $input The raw content without Front-matter
     *
     * @return string
     */
    public function convert($input)
    {
        if (is_string($input) === false) {
            throw new \InvalidArgumentException('Expected a string value at Parsedown converter.');
        }

        $converter = new \ParsedownExtra();

        return $converter->text($input);
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
