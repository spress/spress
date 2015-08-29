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

use Yosymfony\Spress\Plugin\CommandDefinition;

class CommandDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandDefinition()
    {
        $definition = new CommandDefinition('i18:texts');
        $definition->addArgument('file');

        $this->assertEquals('i18:texts', $definition->getName());
        $this->assertCount(1, $definition->getArguments());
    }
}
