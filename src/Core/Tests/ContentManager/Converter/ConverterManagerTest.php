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

class ConverterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertertContent()
    {
        $cm = new ConverterManager();
        $cm->addConverter(new MapConverter());
        $cm->addConverter(new MichelfMarkdownConverter(['md']));
        $result = $cm->convertContent('# My h1 content', 'md');

        $this->assertEquals("<h1>My h1 content</h1>\n", $result->getResult());
        $this->assertEquals('html', $result->getExtension());

        $result = $cm->convertContent('My custom content', 'txt');
        $this->assertEquals('My custom content', $result->getResult());
        $this->assertEquals('txt', $result->getExtension());
    }
}
