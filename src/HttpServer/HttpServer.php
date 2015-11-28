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

/**
 * A simple built-in server.
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
    private $onBeforeRequestFunction;
    private $onAfterRequestFunction;
    private $defatultMimeType = 'application/octet-stream';
    private $errorDocument = 'error.html.twig';

    /**
     * Constructor.
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
        \Twig_Autoloader::register();

        $this->io = $io;
        $this->port = $port;
        $this->host = $host;
        $this->documentroot = $documentroot;
        $this->buildTwig($serverroot);
        $this->requestHandler = new RequestHandler(function (Request $request) {

            $statusCode = 200;
            $content = '';
            $contentType = 'text/html';
            $resourcePath = $this->resolvePath($request->getPathInfo());

            try {
                $this->handleOnBeforeRequestFunction($request, $resourcePath);

                if (file_exists($resourcePath) === true) {
                    $content = file_get_contents($resourcePath);
                    $contentType = $this->getMimeTypeFile($resourcePath);
                } else {
                    $statusCode = 404;
                    $content = $resourcePath;
                }

                $this->handleOnAfterRequestFunction($content, $statusCode);
            } catch (\Exception $e) {
                $this->logRequest($request, 500);

                return $this->buildResponse($e->getMessage(), 500, $contentType);
            }

            $this->logRequest($request, $statusCode);

            return $this->buildResponse($content, $statusCode, $contentType);
        });

        $this->requestHandler
            ->listen($port, $host)
            ->enableHttpFoundationRequest();
    }

    /**
     * Run the built-in server.
     */
    public function start()
    {
        $this->initialMessage();
        $server = new \Yosymfony\HttpServer\HttpServer($this->requestHandler);
        $server->start();
    }

    /**
     * Runs before handle a request.
     *
     * @param callabe $callback
     */
    public function onBeforeRequest(callable $callback)
    {
        $this->onBeforeRequestFunction = $callback;
    }

    /**
     * Runs after handle a request.
     *
     * @param callabe $callback
     */
    public function onAfterRequest(callable $callback)
    {
        $this->onAfterRequestFunction = $callback;
    }

    private function handleOnBeforeRequestFunction(Request $request, $resourcePath)
    {
        if ($this->onBeforeRequestFunction) {
            call_user_func($this->onBeforeRequestFunction, $request, $resourcePath, $this->io);
        }
    }

    private function handleOnAfterRequestFunction($content, $statusCode)
    {
        if ($this->onAfterRequestFunction) {
            call_user_func($this->onAfterRequestFunction, $content, $statusCode, $this->io);
        }
    }

    private function buildResponse($content, $statusCode = 200, $contentType = 'text/html')
    {
        $response = [
            'content' => $content,
            'headers' => ['Content-Type' => $contentType],
            'status_code' => $statusCode,
        ];

        if ($statusCode >= 400) {
            $model = $this->getErrorModel($statusCode, $content);

            $response['content'] = $this->twig->render($this->errorDocument, $model);
        }

        return $response;
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

    private function resolvePath($resourcePath)
    {
        $path = $this->documentroot.$resourcePath;

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
        $options = [
            'cache' => false,
            'autoescape' => false,
        ];

        $loader = new \Twig_Loader_Filesystem();
        $loader->addPath($templateDir);

        $this->twig = new \Twig_Environment($loader, $options);
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
}
