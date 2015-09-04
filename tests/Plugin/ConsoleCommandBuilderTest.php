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

use Symfony\Component\EventDispatcher\EventDispatcher;
use Yosymfony\Spress\Core\Plugin\PluginManager;
use Yosymfony\Spress\Plugin\CommandDefinition;
use Yosymfony\Spress\Plugin\ConsoleCommandBuilder;

class ConsoleCommandBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildCommands()
    {
        $definition = new CommandDefinition('self-update');

        $input = $this->getMockBuilder('\Symfony\Component\Console\Input\InputInterface')
            ->getMock();
        $output = $this->getMockBuilder('\Symfony\Component\Console\Output\OutputInterface')
            ->getMock();

        $commandPluginMock = $this->getMockBuilder('\Yosymfony\Spress\Plugin\CommandPlugin')
            ->setMethods(['getCommandDefinition', 'executeCommand'])
            ->getMock();
        $commandPluginMock->expects($this->once())
            ->method('getCommandDefinition')
            ->will($this->returnValue($definition));
        $commandPluginMock->expects($this->once())
            ->method('executeCommand');

        $pm = new PluginManager(new EventDispatcher());
        $pm->addPlugin('emptyCommandPlugin', $commandPluginMock);

        $builder = new ConsoleCommandBuilder($pm);
        $symfonyConsoleCommands = $builder->buildCommands();

        $this->assertTrue(is_array($symfonyConsoleCommands));
        $this->assertCount(1, $symfonyConsoleCommands);

        $symfonyConsoleCommands[0]->run($input, $output);
    }
}
