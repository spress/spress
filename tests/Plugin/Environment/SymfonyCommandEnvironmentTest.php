<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Tests\Plugin\Environment;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\ConsoleOutput;
use Yosymfony\Spress\Plugin\Environment\SymfonyCommandEnvironment;

class SymfonyCommandEnvironmentTest extends TestCase
{
    public function testHasCommand()
    {
        $application = new Application();
        $application->setAutoExit(false);

        $command = new Command('acme');
        $command->setCode(function ($input, $output) {
            $output->writeln('acme');
        });

        $application->add($command);

        $environment = new SymfonyCommandEnvironment($command, new ConsoleOutput());

        $this->assertTrue($environment->hasCommand('acme'));
        $this->assertFalse($environment->hasCommand('foo'));
    }

    public function testRunCommand()
    {
        $token = 0;
        $application = new Application();
        $application->setAutoExit(false);

        $command = new Command('acme');
        $command->setCode(function ($input, $output) use (&$token) {
            $token = 1;
        });

        $application->add($command);

        $environment = new SymfonyCommandEnvironment($command, new ConsoleOutput());

        $environment->runCommand('acme', []);

        $this->assertEquals(1, $token);
    }

    /**
     * @expectedException Yosymfony\Spress\Plugin\Environment\CommandNotFoundException
     * @expectedExceptionMessage Command "foo" not found.
     */
    public function testRunCommandNotFound()
    {
        $application = new Application();
        $application->setAutoExit(false);

        $command = new Command('acme');
        $command->setCode(function ($input, $output) {
            $output->writeln('acme');
        });

        $application->add($command);

        $environment = new SymfonyCommandEnvironment($command, new ConsoleOutput());

        $environment->runCommand('foo', []);
    }
}
