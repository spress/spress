<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\IO;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\IO\NullIO;

class NullIOTest extends TestCase
{
    public function testIsInteractive()
    {
        $nullIO = new NullIO();

        $this->assertFalse($nullIO->isInteractive());
    }

    public function testIsVerbose()
    {
        $nullIO = new NullIO();

        $this->assertFalse($nullIO->isVerbose());
    }

    public function testIsVeryVerbose()
    {
        $nullIO = new NullIO();

        $this->assertFalse($nullIO->isVeryVerbose());
    }

    public function testIsDebug()
    {
        $nullIO = new NullIO();

        $this->assertFalse($nullIO->isDebug());
    }

    public function testIsDecorated()
    {
        $nullIO = new NullIO();

        $this->assertFalse($nullIO->isDecorated());
    }

    public function testAsk()
    {
        $nullIO = new NullIO();

        $this->assertEquals('default', $nullIO->ask('Is valid?', 'default'));
    }

    public function testAskConfirmation()
    {
        $nullIO = new NullIO();

        $this->assertEquals('default', $nullIO->askConfirmation('Is valid?', 'default'));
    }

    public function testAskAndValidate()
    {
        $nullIO = new NullIO();

        $this->assertEquals('default', $nullIO->askAndValidate('Is valid?', function () {
            return 'validator';
        }, 10, 'default'));
    }
}
