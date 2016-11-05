<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Tests\IO;

use Yosymfony\Spress\IO\BufferIO;

class BufferIOTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOutput()
    {
        $io = new BufferIO();
        $io->write('Hello');

        $this->assertEquals("Hello\n", $io->getOutput());
    }
}
