<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\HttpServer;

/**
 * Server response.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ServerResponse
{
    private $content;
    private $statusCode;
    private $contentType;

    /**
     * Constructor.
     *
     * @param string $content     Content of the response.
     * @param int    $statusCode  The status code.
     * @param string $contentType The MIME type.
     */
    public function __construct($content, $statusCode = 200, $contentType = 'text/html')
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->contentType = $contentType;
    }

    /**
     * Set the content of the response.
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get the content of the response.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the status code. e.g: 200 for ok response.
     *
     * @param int $code
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
    }

    /**
     * Get the status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set the MIME type. e.g: 'text/html'.
     *
     * @param string $type
     */
    public function setContentType($type)
    {
        $this->contentType = $type;
    }

    /**
     * Get the MIME type.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }
}
