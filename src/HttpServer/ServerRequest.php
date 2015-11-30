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

use Dflydev\ApacheMimeTypes\PhpRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Server request.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ServerRequest
{
    private $request;
    private $documentroot;

    /**
     * Constructor.
     * 
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param string                                   $documentroot
     */
    public function __construct(Request $request, $documentroot)
    {
        $this->request = $request;
        $this->documentroot = $documentroot;
    }

    /**
     * Gets the path. e.g: /index.html.
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->request->getPathInfo();
    }

    /**
     * Gets the absolute path.
     * 
     * @return string Absolute path using the document root.
     */
    public function getAbsolutePath()
    {
        $path = $this->documentroot.$this->getPath();

        if (is_dir($path)) {
            $path .= '/index.html';
        }

        return $path;
    }

    /**
     * Gets the client IP.
     * 
     * @return string
     */
    public function getIp()
    {
        return $this->request->getClientIp();
    }

    /**
     * Gets the Mime Type.
     * 
     * @return string. Myme type. "application/octet-stream" by default.
     */
    public function getMimeType()
    {
        $mimetypeRepo = new PhpRepository();
        $path = $this->getAbsolutePath();

        return $mimetypeRepo->findType(pathinfo($path, PATHINFO_EXTENSION)) ?: 'application/octet-stream';
    }
}
