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

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\ContentManager\Converter\MapConverter;

class MapConverterTest extends TestCase
{
    public function testMapConverter()
    {
        $converter = new MapConverter();

        $this->assertEquals(0, $converter->getPriority());
    }

    public function testMatchesAnyFilenameExtension()
    {
        $converter = new MapConverter();

        $this->assertTrue($converter->matches('html'));
        $this->assertTrue($converter->matches('md'));
    }

    public function testUnalteredContent()
    {
        $converter = new MapConverter();

        $this->assertEquals('<h1>This is HTML text</h1>', $converter->convert('<h1>This is HTML text</h1>'));
        $this->assertEquals('This is plain text', $converter->convert('This is plain text'));
    }

    public function testMappingFilenameExtension()
    {
        $converter = new MapConverter([
            'twig' => 'html',
            'html.twig' => 'html',
            'twig.html' => 'html',
        ]);

        $this->assertEquals('html', $converter->getOutExtension('twig'));
        $this->assertEquals('html', $converter->getOutExtension('html.twig'));
        $this->assertEquals('html', $converter->getOutExtension('twig.html'));
    }

    public function testExtensionNotFoundMappingTable()
    {
        $converter = new MapConverter([
            'twig' => 'html',
        ]);

        $this->assertEquals('md', $converter->getOutExtension('md'));
    }
}
