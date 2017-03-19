<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Tests\Coverter;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Converter\ParsedownConverter;

class ParsedownConverterTest extends TestCase
{
    public function testConverter()
    {
        $converter = new ParsedownConverter(['markdown', 'mkd', 'mkdn', 'md']);

        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentManager\Converter\ConverterInterface', $converter);
        $this->assertEquals(1, $converter->getPriority());
        $this->assertTrue($converter->matches('md'));
        $this->assertEquals('<h1>hi</h1>', $converter->convert('#hi'));
        $this->assertEquals('html', $converter->getOutExtension('md'));
    }
}
