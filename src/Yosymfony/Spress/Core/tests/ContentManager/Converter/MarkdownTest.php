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
use Yosymfony\Spress\Core\ContentManager\Converter\Markdown;

class MarkdownTest extends \PHPUnit_Framework_TestCase
{
    public function testMarkdown()
    {
        $converter = new Markdown();
        $converter->initialize(['markdown_ext' => ['markdown','mkd','mkdn','md']]);
        
        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentManager\ConverterInterface', $converter);
        $this->assertEquals(1, $converter->getPriority());
        $this->assertTrue(is_array($converter->getSupportExtension()));
        $this->assertCount(4, $converter->getSupportExtension());
        $this->assertTrue($converter->matches('md'));
        $this->assertEquals("<h1>hi</h1>\n", $converter->convert('#hi'));
        $this->assertEquals('html', $converter->getOutExtension('md'));
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testMarkdownBadConfig()
    {
        $converter = new Markdown();
        $converter->initialize([]);
    }
}