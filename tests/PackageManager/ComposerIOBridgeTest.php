<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Test\PackageManager;

use Yosymfony\Spress\PackageManager\ComposerIOBridge;
use Yosymfony\Spress\Core\IO\NullIO;

class ComposerIOBridgeTest extends \PHPUnit_Framework_TestCase
{
    public function testVerbosity()
    {
        $ioBridge = new ComposerIOBridge(new NullIO());

        $this->assertFalse($ioBridge->isVerbose());
        $this->assertFalse($ioBridge->isVeryVerbose());
    }

    public function testIsDebug()
    {
        $ioBridge = new ComposerIOBridge(new NullIO());

        $this->assertFalse($ioBridge->isDebug());
    }

    public function testIsInteractive()
    {
        $ioBridge = new ComposerIOBridge(new NullIO());

        $this->assertFalse($ioBridge->isInteractive());
    }

    public function testIsDecorated()
    {
        $ioBridge = new ComposerIOBridge(new NullIO());

        $this->assertFalse($ioBridge->isDecorated());
    }

    public function testAsk()
    {
        $ioBridge = new ComposerIOBridge(new NullIO());

        $this->assertEquals('Yo! Symfony', $ioBridge->ask('Name', 'Yo! Symfony'));
    }

    public function testAskConfirmation()
    {
        $ioBridge = new ComposerIOBridge(new NullIO());

        $this->assertTrue($ioBridge->askConfirmation('Really?', true));
    }

    public function testAskAndValidate()
    {
        $ioBridge = new ComposerIOBridge(new NullIO());

        $this->assertNull($ioBridge->askAndValidate('Really?', function ($value) {
        }));
    }
}
