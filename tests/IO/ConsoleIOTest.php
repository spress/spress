<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\tests\IO;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Yosymfony\Spress\IO\ConsoleIO;

class ConsoleIOTest extends \PHPUnit_Framework_TestCase
{
    protected $command;
    protected $tester;

    protected function setUp()
    {
        $this->command = new Command('consoleIO');
        $this->tester = new CommandTester($this->command);
    }

    protected function tearDown()
    {
        $this->command = null;
        $this->tester = null;
    }

    public function testIsInteractive()
    {
        $isInteractive = false;

        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isInteractive) {
            $io = new ConsoleIO($input, $output);

            $this->assertEquals($isInteractive, $io->isInteractive());
        });

        $this->tester->execute([], ['interactive' => false]);

        $isInteractive = true;

        $this->tester->execute([], ['interactive' => true]);
    }

    public function testIsVerbose()
    {
        $isVerbose = true;

        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isVerbose) {
            $io = new ConsoleIO($input, $output);

            $this->assertEquals($isVerbose, $io->isVerbose());
        });

        $this->tester->execute([], ['verbosity' => 2]);

        $isVerbose = false;

        $this->tester->execute([], ['verbosity' => 0]);
    }

    public function testIsVeryVerbose()
    {
        $isVeryVerbose = true;

        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isVeryVerbose) {
            $io = new ConsoleIO($input, $output);

            $this->assertEquals($isVeryVerbose, $io->isVeryVerbose());
        });

        $this->tester->execute([], ['verbosity' => 3]);

        $isVeryVerbose = false;

        $this->tester->execute([], ['verbosity' => 2]);
    }

    public function testIsDebug()
    {
        $isDebug = true;

        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDebug) {
            $io = new ConsoleIO($input, $output);

            $this->assertEquals($isDebug, $io->isDebug());
        });

        $this->tester->execute([], ['verbosity' => 4]);

        $isDebug = false;

        $this->tester->execute([], ['verbosity' => 3]);
    }

    public function testIsDecorated()
    {
        $isDecorated = true;

        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDecorated) {
            $io = new ConsoleIO($input, $output);

            $this->assertEquals($isDecorated, $io->isDecorated());
        });

        $this->tester->execute([], ['decorated' => true]);

        $isDecorated = false;

        $this->tester->execute([], ['decorated' => false]);
    }

    public function testWrite()
    {
        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDecorated) {
            $io = new ConsoleIO($input, $output);
            $io->write('Hi IO API');
        });

        $this->tester->execute([], ['interactive' => false, 'decorated' => false]);

        $this->assertEquals("Hi IO API\n", $this->tester->getDisplay(true));
    }

    public function testAsk()
    {
        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDecorated) {
            $io = new ConsoleIO($input, $output);
            $result = $io->ask('what is your name?', 'Yo! Symfony');

            $this->assertEquals('Yo! Symfony', $result);
        });

        $this->tester->execute([], ['interactive' => false, 'decorated' => false]);
    }

    public function testAskConfirmation()
    {
        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDecorated) {
            $io = new ConsoleIO($input, $output);

            $this->assertTrue($io->AskConfirmation('Are you sure?'));
            $this->assertFalse($io->AskConfirmation('Are you sure?', false));
        });

        $this->tester->execute([], ['interactive' => false, 'decorated' => false]);
    }

    public function testAskAndValidate()
    {
        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDecorated) {
            $io = new ConsoleIO($input, $output);
            $result = $io->askAndValidate(
                'what is your name?',
                function ($value) {
                    return $value === 'Yo! Symfony';
                },
                false,
                'Yo! Symfony');

            $this->assertEquals('Yo! Symfony', $result);
        });

        $this->tester->execute([], ['interactive' => false, 'decorated' => false]);
    }

    public function testSuccess()
    {
        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDecorated) {
            $io = new ConsoleIO($input, $output);
            $io->success('success!');
        });

        $this->tester->execute([], ['interactive' => false, 'decorated' => false]);

        $this->assertRegExp("/\[OK\] success!/", $this->tester->getDisplay(true));
    }

    public function testWarning()
    {
        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDecorated) {
            $io = new ConsoleIO($input, $output);
            $io->warning('warning!');
        });

        $this->tester->execute([], ['interactive' => false, 'decorated' => false]);

        $this->assertRegExp("/\[WARNING\] warning!/", $this->tester->getDisplay(true));
    }

    public function testListing()
    {
        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDecorated) {
            $io = new ConsoleIO($input, $output);
            $io->listing(['element 1']);
        });

        $this->tester->execute([], ['interactive' => false, 'decorated' => false]);

        $this->assertRegExp("/\* element 1/", $this->tester->getDisplay(true));
    }

    public function testLabelValue()
    {
        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDecorated) {
            $io = new ConsoleIO($input, $output);
            $io->labelValue('Items', 20);
        });

        $this->tester->execute([], ['interactive' => false, 'decorated' => false]);

        $this->assertEquals("Items: 20\n", $this->tester->getDisplay(true));
    }

    public function testNewLine()
    {
        $this->command->setCode(function (InputInterface $input, OutputInterface $output) use (&$isDecorated) {
            $io = new ConsoleIO($input, $output);
            $io->newLine();
        });

        $this->tester->execute([], ['interactive' => false, 'decorated' => false]);

        $this->assertEquals("\n", $this->tester->getDisplay(true));
    }
}
