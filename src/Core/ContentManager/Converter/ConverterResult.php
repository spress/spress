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
 * Converter result.
 *
 * @api
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConverterResult
{
    private $inputExtension;
    private $outputExtension;
    private $result;

    /**
     * Constructor.
     *
     * @param string $result          Result of applied the converter.
     * @param string $inputExtension  The input extension.
     * @param string $outputExtension The output extension.
     */
    public function __construct($result, $inputExtension, $outputExtension)
    {
        $this->inputExtension = $inputExtension;
        $this->result = $result;
        $this->outputExtension = $outputExtension;
    }

    /**
     * Get Result of converter data.
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * The output extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->outputExtension;
    }

    /**
     * The input extension.
     * 
     * @return string
     */
    public function getInputExtension()
    {
        return $this->inputExtension;
    }
}
