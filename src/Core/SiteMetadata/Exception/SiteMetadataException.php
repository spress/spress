<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\SiteMetadata\Exception;

/**
 * Exception class thrown when the there is a problem loading the site metadata.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SiteMetadataException extends \RuntimeException
{
    private $resource;

    /**
     * Constructor.
     *
     * @param string $message The exception message to throw.
     * @param int $code The exception code.
     * @param Exception $previous The previous exception used for the exception chaining.
     * @param string $resource A resource could be the path of a file.
     */
    public function __construct($message, $code = 0, \Exception $previous = null, $resource = null)
    {
        $this->resource = $resource;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the resource. e.g: the path of a filename.
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }
}
