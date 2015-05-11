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

/**
 * Interface for converters
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface ConverterInterface
{
    /**
     * Initialize the converter
     *
     * @param array $config Configuration parameters
     */
    public function initialize(array $config);

    /**
     * Get the priority of converter
     *
     * @return int Value between 0 to 10 with great is more priority
     */
    public function getPriority();

    /**
     * If file's extension is support by converter
     *
     * @param string $extension Extension without dot
     *
     * @return boolean
     */
    public function matches($extension);

    /**
     * Convert the input data
     *
     * @param string $input The raw content without Front-matter
     *
     * @return string
     */
    public function convert($input);

    /**
     * The extension of filename result (without dot). E.g: html.
     *
     * @param string $extension File's extension
     *
     * @return string
     */
    public function getOutExtension($extension);
}
