<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\Tests\ContentManager\Converter;

use Yosymfony\Spress\Core\Application;
use Yosymfony\Spress\Core\ContentManager\Converter\Mirror;

class MirrorTest extends \PHPUnit_Framework_TestCase
{
    public function testMarkDown()
    {
        $converter = new Mirror();
        $converter->initialize([]);
        
        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentManager\ConverterInterface', $converter);
        $this->assertEquals(0, $converter->getPriority());
        $this->assertTrue(is_array($converter->getSupportExtension()));
        $this->assertCount(0, $converter->getSupportExtension());
        $this->assertTrue($converter->matches('myExt'));
        $this->assertEquals("<h1>hi</h1>", $converter->convert('<h1>hi</h1>'));
        $this->assertEquals('md', $converter->getOutExtension('md'));
        $this->assertEquals('myExt', $converter->getOutExtension('myExt'));
    }
}