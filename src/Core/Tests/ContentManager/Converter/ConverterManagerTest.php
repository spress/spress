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

use Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager;
use Yosymfony\Spress\Core\ContentManager\Converter\MichelfMarkdownConverter;
use Yosymfony\Spress\Core\ContentManager\Converter\MapConverter;
use Yosymfony\Spress\Core\DataSource\Item;

class ConverterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddConverter()
    {
        $cm = new ConverterManager();
        $cm->addConverter(new MapConverter());

        $this->assertEquals(1, $cm->countConverter());
    }

    public function testConvertertMarkdownContent()
    {
        $cm = new ConverterManager();
        $cm->addConverter(new MichelfMarkdownConverter(['md']));
        $result = $cm->convertContent('# My h1 content', 'md');

        $this->assertEquals("<h1>My h1 content</h1>\n", $result->getResult());
        $this->assertEquals('html', $result->getExtension());
    }

    public function testConvertItemWithTextExtension()
    {
        $cm = new ConverterManager(['html.twig']);
        $cm->addConverter(new MapConverter(['html.twig' => 'html']));

        $item = new Item('text', 'about.html.twig');
        $item->setPath('about.html.twig', Item::SNAPSHOT_PATH_RELATIVE);

        $result = $cm->convertItem($item);

        $this->assertEquals('text', $result->getResult());
        $this->assertEquals('html', $result->getExtension());
        $this->assertEquals('html.twig', $result->getInputExtension());
    }

    public function testConvertItemNoTextExtension()
    {
        $cm = new ConverterManager(['html']);
        $cm->addConverter(new MapConverter());

        $item = new Item('text', 'file.unknow');
        $item->setPath('file.unknow', Item::SNAPSHOT_PATH_RELATIVE);

        $result = $cm->convertItem($item);

        $this->assertEquals('text', $result->getResult());
        $this->assertEquals('unknow', $result->getExtension());
    }

    public function testClearConverter()
    {
        $cm = new ConverterManager();
        $cm->addConverter(new MapConverter());
        $cm->clearConverter();

        $this->assertEquals(0, $cm->countConverter());
    }
}
