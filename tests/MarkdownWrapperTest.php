<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Tests;

use Yosymfony\Spress\MarkdownWrapper;

class MarkdownWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testMarkdown()
    {
        $md = new MarkdownWrapper();
        
        $this->assertEquals("<p>hello world</p>\n", $md->parse('hello world'));
    }
}