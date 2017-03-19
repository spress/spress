<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Tests\HttpServer;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\HttpServer\ServerRequest;

class ServerRequestTest extends TestCase
{
    protected $documentRoot;
    protected $serverRoot;

    public function setUp()
    {
        $this->documentRoot = __DIR__.'/../fixtures/httpServer';
        $this->serverRoot = __DIR__.'/../../app/httpServer';
    }

    public function testRequest()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();
        $request->method('getPathInfo')
            ->willReturn('/');
        $request->method('getClientIp')
            ->willReturn('127.0.0.1');

        $serverRequest = new ServerRequest($request, $this->documentRoot, $this->serverRoot);

        $this->assertEquals('/', $serverRequest->getPath());
        $this->assertEquals($this->documentRoot.'/index.html', $serverRequest->getPathFilename());
        $this->assertEquals('text/html', $serverRequest->getMimeType());
        $this->assertEquals('127.0.0.1', $serverRequest->getIp());
        $this->assertFalse($serverRequest->isInternal());
    }

    public function testInternalRequest()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request', $this->documentRoot, $this->serverRoot)
            ->getMock();
        $request->method('getPathInfo')
            ->willReturn('/@internal/bootstrap.min.css');
        $request->method('getClientIp')
            ->willReturn('127.0.0.1');

        $serverRequest = new ServerRequest($request, $this->documentRoot, $this->serverRoot);

        $this->assertEquals('/bootstrap.min.css', $serverRequest->getPath());
        $this->assertEquals($this->serverRoot.'/bootstrap.min.css', $serverRequest->getPathFilename());
        $this->assertEquals('text/css', $serverRequest->getMimeType());
        $this->assertEquals('127.0.0.1', $serverRequest->getIp());
        $this->assertTrue($serverRequest->isInternal());
    }
}
