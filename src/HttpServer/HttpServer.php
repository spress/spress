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

use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\HttpServer\RequestHandler;

/**
 * A simple HTTP server.
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
    private $serverroot;
    private $requestHandler;
    private $onBeforeRequestFunction;
    private $onAfterRequestFunction;
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
        $this->io = $io;
        $this->port = $port;
        $this->host = $host;
        $this->serverroot = $serverroot;
        $this->documentroot = $documentroot;
        $this->buildTwig($serverroot);
        $this->requestHandler = new RequestHandler(function (Request $request) {
            $serverResponse = new ServerResponse('');
            $serverRequest = new ServerRequest($request, $this->documentroot, $this->serverroot);

            try {
                $this->handleOnBeforeRequestFunction($serverRequest);
                $resourcePath = $serverRequest->getPathFilename();

                if (file_exists($resourcePath) === true) {
                    $serverResponse->setContent(file_get_contents($resourcePath));
                    $serverResponse->setContentType($serverRequest->getMimeType());
                } else {
                    $serverResponse->setStatusCode(404);
                    $serverResponse->setContent($resourcePath);
                }

                $this->handleOnAfterRequestFunction($serverResponse);
            } catch (\Exception $e) {
                $serverResponse->setStatusCode(500);
                $serverResponse->setContent($e->getMessage());
            }

            $this->logRequest($serverRequest->getIp(), $serverRequest->getPath(), $serverResponse->getStatusCode());

            return $this->buildFinalResponse($serverResponse);
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
     * @param callable $callback Callback should be a function with
     *                           the following signature:
     *
     * ```php
     * function(ServerRequest $request)
     * {
     *     // ...
     * }
     * ```
     */
    public function onBeforeRequest(callable $callback)
    {
        $this->onBeforeRequestFunction = $callback;
    }

    /**
     * Runs after handle a request.
     *
     * @param callable $callback Callback should be a function with
     *                           the following signature:
     *
     * ```php
     * function(ServerResponse $response)
     * {
     *     // ...
     * }
     * ```
     */
    public function onAfterRequest(callable $callback)
    {
        $this->onAfterRequestFunction = $callback;
    }

    private function handleOnBeforeRequestFunction(ServerRequest $request)
    {
        if ($this->onBeforeRequestFunction) {
            call_user_func($this->onBeforeRequestFunction, $request);
        }
    }

    private function handleOnAfterRequestFunction(ServerResponse $response)
    {
        if ($this->onAfterRequestFunction) {
            call_user_func($this->onAfterRequestFunction, $response);
        }
    }

    private function buildFinalResponse(ServerResponse $response)
    {
        $finalResponse = [
            'content' => $response->getContent(),
            'headers' => ['Content-Type' => $response->getContentType()],
            'status_code' => $response->getStatusCode(),
        ];

        if ($finalResponse['status_code'] >= 400) {
            $model = $this->getErrorModel($finalResponse['status_code'], $finalResponse['content']);

            $finalResponse['content'] = $this->twig->render($this->errorDocument, $model);
        }

        return $finalResponse;
    }

    private function logRequest($ip, $path, $statusCode)
    {
        $date = new \Datetime();
        $data = sprintf(
            '[%s] %s [%s] %s',
            $date->format('Y-m-d h:i:s'),
            $ip,
            $statusCode,
            $path
        );

        if ($statusCode >= 400) {
            $data = '<error>'.$data.'</error>';
        }

        $this->io->write($data);
    }

    private function buildTwig($templateDir)
    {
        $options = [
            'cache' => false,
            'autoescape' => false,
        ];

        $loader = new FilesystemLoader();
        $loader->addPath($templateDir);

        $this->twig = new Environment($loader, $options);
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
        $this->io->newLine();
        $this->io->write('<comment>Spress server running... press ctrl-c to stop</comment>');
        $this->io->write(sprintf(
            '<comment>Port: %s Host: %s Document root: %s</comment>',
            $this->port,
            $this->host,
            $this->documentroot
        ));
        $this->io->newLine();
    }
}
