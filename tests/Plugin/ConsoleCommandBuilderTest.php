<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Tests\Plugin;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
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

        $input = new ArrayInput([]);
        $input->setInteractive(false);

        $output = new StreamOutput(fopen('php://memory', 'w', false));

        $commandPluginMock = $this->getMockBuilder('\Yosymfony\Spress\Plugin\CommandPlugin')
            ->getMock();
        $commandPluginMock->expects($this->once())
            ->method('getCommandDefinition')
            ->will($this->returnValue($definition));
        $commandPluginMock->expects($this->once())
            ->method('executeCommand');

        $pm = new PluginManager(new EventDispatcher());
        $pm->getPluginCollection()->add('emptyCommandPlugin', $commandPluginMock);

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
