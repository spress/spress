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
use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\HttpServer\RequestHandler;
use Yosymfony\Spress\Core\TwigFactory;

/**
 * Built-in server
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class HttpServer
{
    private $io;
    private $twig;
    private $port;
    private $host;
    private $documentroot;
    private $requestHandler;
    private $onBeforeHandleRequestFunction;
    private $defatultMimeType = 'application/octet-stream';
    private $errorDocument = 'error.html.twig';

    /**
     * Constructor
     *
     * @param IOInterface $io
     * @param TwigFactory $twigFactory
     * @param string      $serverroot
     * @param string      $documentroot
     * @param int         $port
     * @param string      $host
     */
    public function __construct(IOInterface $io, $serverroot, $documentroot, $port, $host)
    {
        $this->io = $io;
        $this->port = $port;
        $this->host = $host;
        $this->documentroot = $documentroot;
        $this->buildTwig($serverroot);
        $this->requestHandler = new RequestHandler(function (Request $request) {

            $resourcePath = $this->resolvePath($request);

            if ($this->onBeforeHandleRequestFunction) {
                try {
                    call_user_func($this->onBeforeHandleRequestFunction, $request, $resourcePath, $this->io);
                } catch (\Exception $e) {
                    $this->logRequest($request, 500);

                    return $this->getResponseError(500, $e->getMessage());
                }
            }

            if (false === file_exists($resourcePath)) {
                $this->logRequest($request, 404);

                return $this->getResponseError(404, $resourcePath);
            }

            $content = file_get_contents($resourcePath);
            $contentType = $this->getMimeTypeFile($resourcePath);

            $this->logRequest($request, 200);

            return $this->getResponseOk($content, $contentType);
        });

        $this->requestHandler
            ->listen($port, $host)
            ->enableHttpFoundationRequest();
    }

    /**
     * Runs before handle a request
     *
     * @param callabe $callback
     */
    public function onBeforeHandleRequest(callable $callback)
    {
        $this->onBeforeHandleRequestFunction = $callback;
    }

    /**
     * Run the built-in server
     */
    public function start()
    {
        $this->initialMessage();
        $server = new \Yosymfony\HttpServer\HttpServer($this->requestHandler);
        $server->start();
    }

    private function initialMessage()
    {
        $this->io->write('');
        $this->io->write('<comment>Spress server running... press ctrl-c to stop</comment>');
        $this->io->write(sprintf(
            '<comment>Port: %s Host: %s Document root: %s</comment>',
            $this->port,
            $this->host,
            $this->documentroot));
    }

    private function logRequest(Request $request, $statusCode)
    {
        $date = new \Datetime();
        $data = sprintf('[%s] %s [%s] %s',
            $date->format('Y-m-d h:i:s'),
            $request->getClientIp(),
            $statusCode,
            $request->getPathInfo());

        if ($statusCode >= 400) {
            $data = '<error>'.$data.'</error>';
        }

        $this->io->write($data);
    }

    private function getResponseOk($content, $contentType)
    {
        return [
            'content' => $content,
            'headers' => ['Content-Type' => $contentType],
            'status_code' => 200
        ];
    }

    private function getResponseError($statusCode, $data)
    {
        $model = $this->getErrorModel($statusCode, $data);

        return [
            'content' => $this->twig->render($this->errorDocument, $model),
            'headers' => ['Content-Type' => 'text/html'],
            'status_code' => $statusCode
        ];
    }

    private function resolvePath(Request $request)
    {
        $path = $this->documentroot.$request->getPathInfo();

        if (is_dir($path)) {
            $path .= '/index.html';
        }

        return $path;
    }

    private function getMimeTypeFile($path)
    {
        $mimetypeRepo = new PhpRepository();

        return $mimetypeRepo->findType(pathinfo($path, PATHINFO_EXTENSION)) ?: $this->defatultMimeType;
    }

    private function buildTwig($templateDir)
    {
        $twigFactory = new TwigFactory();
        $this->twig = $twigFactory
            ->withAutoescape(false)
            ->withCache(false)
            ->addLoaderFilesystem($templateDir)
            ->create();
    }

    private function getErrorModel($statusCode, $data)
    {
        switch ($statusCode) {
            case 404:
                $message = sprintf('Resource not found: %s', $data);
                break;

            case 500:
                $message = sprintf('Server exception: %s', $data);
                break;

            default:
                $message = $data;
        }

        return [
            'status_code' => $statusCode,
            'message' => $message,
        ];
    }
}
