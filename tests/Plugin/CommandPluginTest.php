<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\tests\Plugin;

use Yosymfony\Spress\Plugin\CommandPlugin;

class CommandPluginTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyPlugin()
    {
        $commandPlugin = new CommandPlugin();

        $this->assertTrue(is_array($commandPlugin->getMetas()));
        $this->assertCount(0, $commandPlugin->getMetas());
    }
}
