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

    public function __construct($content, $statusCode = 200, $contentType = 'text/html')
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->contentType = $contentType;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setStatusCode($code)
    {
        $this->statusCode = $code;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setContentType($type)
    {
        $this->contentType = $type;
    }

    public function getContentType()
    {
        return $this->contentType;
    }
}
