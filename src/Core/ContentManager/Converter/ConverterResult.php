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
 * Converter result
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConverterResult
{
    private $extension;
    private $result;

    /**
     * Constructor
     *
     * @param string $result    Result of converter data
     * @param string $extension Extension of result
     */
    public function __construct($result, $extension)
    {
        $this->result = $result;
        $this->extension = $extension;
    }

    /**
     * Get Result of converter data
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Extension of result
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }
}
