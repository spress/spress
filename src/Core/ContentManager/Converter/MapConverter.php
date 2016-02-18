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
 * This converter maps an extension to another using the
 * mapping table passed in the constructor. If the extension not
 * exists, this converter returns the same input extension.
 *
 * This converter does not alter the content.
 *
 * @author Victor Pueras <vpgugr@gmail.com>
 */
class MapConverter implements ConverterInterface
{
    private $extensionMap = [];

    /**
     * Constructor.
     * 
     * @param array $fileExtensionMap An extension map. e.g:
     * 
     * ```php
     * $converter = new MappingConverter([
     *   'twig'      => 'html',
     *   'twig.html' => 'html',
     * ]);
     * ```
     */
    public function __construct(array $fileExtensionMap = [])
    {
        $this->extensionMap = $fileExtensionMap;
    }

    /**
     * Get the converter priority.
     *
     * @return int
     */
    public function getPriority()
    {
        return 0;
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
        return true;
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
        return $input;
    }

    /**
     * The extension of filename result (without dot). E.g: 'html'.
     *
     * @param string $extension File's extension
     *
     * @return string
     */
    public function getOutExtension($extension)
    {
        if (isset($this->extensionMap[$extension])) {
            return $this->extensionMap[$extension];
        }

        return $extension;
    }
}
