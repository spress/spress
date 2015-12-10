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
use Yosymfony\Spress\Core\Support\StringWrapper;

/**
 * Server request.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ServerRequest
{
    private $request;
    private $documentRoot;
    private $serverRoot;
    private $internalPrefix = '/@internal';

    /**
     * Constructor.
     * 
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param string                                   $documentRoot Path to the document root.
     * @param string                                   $serverRoot   Path to the server root.
     */
    public function __construct(Request $request, $documentRoot, $serverRoot)
    {
        $this->request = $request;
        $this->documentRoot = $documentRoot;
        $this->serverRoot = $serverRoot;
    }

    /**
     * Gets the path. e.g: /index.html.
     * 
     * @return string
     */
    public function getPath()
    {
        return str_replace($this->internalPrefix, '', $this->request->getPathInfo());
    }

    /**
     * Gets the path with "index.html" append.
     * 
     * @return string Absolute path using either document root or server root (iternal requests).
     */
    public function getPathFilename()
    {
        $path = $this->getPath();

        if ($this->isInternal()) {
            return $this->serverRoot.$path;
        }

        $path = $this->documentRoot.$path;

        if (is_dir($path) === true) {
            $path .= '/index.html';
        }

        return preg_replace('/\/\/+/', '/', $path);
    }

    /**
     * Is internal resource?
     * 
     * @return bool
     */
    public function isInternal()
    {
        return (new StringWrapper($this->request->getPathInfo()))->startWith($this->internalPrefix);
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
        $path = $this->getPathFilename();

        return $mimetypeRepo->findType(pathinfo($path, PATHINFO_EXTENSION)) ?: 'application/octet-stream';
    }
}
