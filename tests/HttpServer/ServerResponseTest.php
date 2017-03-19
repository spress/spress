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
use Yosymfony\Spress\HttpServer\ServerResponse;

class ServerResponseTest extends TestCase
{
    public function testResponse()
    {
        $serverResponse = new ServerResponse('Hola user!');

        $this->assertEquals('Hola user!', $serverResponse->getContent());
        $this->assertEquals(200, $serverResponse->getStatusCode());
        $this->assertEquals('text/html', $serverResponse->getContentType());

        $serverResponse->setContent('Hola user again!');

        $this->assertEquals('Hola user again!', $serverResponse->getContent());

        $serverResponse->setStatusCode(204);

        $this->assertEquals(204, $serverResponse->getStatusCode());

        $serverResponse->setContentType('text/xml');

        $this->assertEquals('text/xml', $serverResponse->getContentType());
    }
}
