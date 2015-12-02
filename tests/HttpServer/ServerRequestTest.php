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

use Yosymfony\Spress\HttpServer\ServerRequest;

class ServerRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testRequest()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();
        $request->method('getPathInfo')
            ->willReturn('/post/hello-world');
        $request->method('getClientIp')
            ->willReturn('127.0.0.1');

        $serverRequest = new ServerRequest($request);

        $this->assertEquals('/post/hello-world', $serverRequest->getPath());
        $this->assertEquals('/post/hello-world/index.html', $serverRequest->getPathFilename());
        $this->assertEquals('text/html', $serverRequest->getMimeType());
        $this->assertEquals('127.0.0.1', $serverRequest->getIp());
        $this->assertFalse($serverRequest->isInternal());
    }

    public function testInternalRequest()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();
        $request->method('getPathInfo')
            ->willReturn('/@internal/bootstrap.min.css');
        $request->method('getClientIp')
            ->willReturn('127.0.0.1');

        $serverRequest = new ServerRequest($request);

        $this->assertEquals('/bootstrap.min.css', $serverRequest->getPath());
        $this->assertEquals('/bootstrap.min.css', $serverRequest->getPathFilename());
        $this->assertEquals('text/css', $serverRequest->getMimeType());
        $this->assertEquals('127.0.0.1', $serverRequest->getIp());
        $this->assertTrue($serverRequest->isInternal());
    }
}
