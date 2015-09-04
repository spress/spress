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
        $definition->setDescription('Update spress.phar to the latest version.');
        $definition->setHelp('The self-update command replace your spress.phar by the latest version.');
        $definition->addOption('all');
        $definition->addArgument('dir');

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

        $this->assertContainsOnlyInstancesOf('Symfony\Component\Console\Command\Command', $symfonyConsoleCommands);

        $symfonyConsoleCommand = $symfonyConsoleCommands[0];

        $this->assertCount(1, $symfonyConsoleCommand->getDefinition()->getOptions());
        $this->assertCount(1, $symfonyConsoleCommand->getDefinition()->getArguments());

        $this->assertEquals('Update spress.phar to the latest version.', $symfonyConsoleCommand->getDescription());
        $this->assertEquals(
            'The self-update command replace your spress.phar by the latest version.',
            $symfonyConsoleCommand->getHelp());

        $symfonyConsoleCommand->run($input, $output);
    }
}
