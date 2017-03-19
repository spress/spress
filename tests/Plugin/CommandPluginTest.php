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

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\ConsoleOutput;
use Yosymfony\Spress\Plugin\CommandPlugin;
use Yosymfony\Spress\Plugin\Environment\SymfonyCommandEnvironment;

class CommandPluginTest extends TestCase
{
    public function testEmptyPlugin()
    {
        $commandPlugin = new CommandPlugin();

        $this->assertTrue(is_array($commandPlugin->getMetas()));
        $this->assertCount(0, $commandPlugin->getMetas());
    }

    public function testGetCommandEnvironment()
    {
        $commandPlugin = new CommandPlugin();

        $this->assertInstanceOf('Yosymfony\Spress\Plugin\Environment\CommandEnvironmentInterface', $commandPlugin->getCommandEnvironment());
    }

    public function testSetCommandEnvironment()
    {
        $command = new Command('acme');
        $command->setCode(function ($input, $output) {
            $output->writeln('acme');
        });

        $commandPlugin = new CommandPlugin();
        $commandPlugin->setCommandEnvironment(new SymfonyCommandEnvironment($command, new ConsoleOutput()));

        $this->assertInstanceOf('Yosymfony\Spress\Plugin\Environment\CommandEnvironmentInterface', $commandPlugin->getCommandEnvironment());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage You must override the "getCommandDefinition" method in the concrete command plugin class.
     */
    public function testCommandDefinitionNotOverrided()
    {
        $commandPlugin = new CommandPlugin();
        $commandPlugin->getCommandDefinition();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage You must override the "executeCommand" method in the concrete command plugin class.
     */
    public function testExecuteCommandNotOverrided()
    {
        $io = $this->getMockBuilder('Yosymfony\Spress\Core\IO\IOInterface')->getMock();

        $commandPlugin = new CommandPlugin();
        $commandPlugin->executeCommand($io, [], []);
    }
}
