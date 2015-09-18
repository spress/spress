<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Based on tests: https://github.com/composer/composer/blob/master/tests/Composer/Test/IO/ConsoleIOTest.php
 * from Nils Adermann <naderman@naderman.de> and Jordi Boggiano <j.boggiano@seld.be>.
 */

namespace Yosymfony\Spress\tests\IO;

use Yosymfony\Spress\IO\ConsoleIO;

class ConsoleIOTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInteractive()
    {
        $inputMock = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock();
        $inputMock->expects($this->at(0))
            ->method('isInteractive')
            ->will($this->returnValue(true));
        $inputMock->expects($this->at(1))
            ->method('isInteractive')
            ->will($this->returnValue(false));
        $outputMock = $this->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
            ->getMock();
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);

        $this->assertTrue($consoleIO->isInteractive());
        $this->assertFalse($consoleIO->isInteractive());
    }

    public function testIsVerbose()
    {
        $inputMock = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock();
        $outputMock = $this->getMockBuilder('Symfony\Component\Console\Output\Output')
             ->setMethods(['getVerbosity', 'doWrite'])
             ->getMock();
        $outputMock->expects($this->at(0))
            ->method('getVerbosity')
            ->will($this->returnValue(2));
        $outputMock->expects($this->at(1))
            ->method('getVerbosity')
            ->will($this->returnValue(0));
        $helperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\HelperSet')
            ->getMock();

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);

        $this->assertTrue($consoleIO->isVerbose());
        $this->assertFalse($consoleIO->isVerbose());
    }

    public function testIsVeryVerbose()
    {
        $inputMock = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock();
        $outputMock = $this->getMockBuilder('Symfony\Component\Console\Output\Output')
             ->setMethods(['getVerbosity', 'doWrite'])
             ->getMock();
        $outputMock->expects($this->at(0))
            ->method('getVerbosity')
            ->will($this->returnValue(3));
        $outputMock->expects($this->at(1))
            ->method('getVerbosity')
            ->will($this->returnValue(2));
        $helperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\HelperSet')
            ->getMock();

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);

        $this->assertTrue($consoleIO->isVeryVerbose());
        $this->assertFalse($consoleIO->isVeryVerbose());
    }

    public function testIsDebug()
    {
        $inputMock = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock();
        $outputMock = $this->getMockBuilder('Symfony\Component\Console\Output\Output')
             ->setMethods(['getVerbosity', 'doWrite'])
             ->getMock();
        $outputMock->expects($this->at(0))
            ->method('getVerbosity')
            ->will($this->returnValue(4));
        $outputMock->expects($this->at(1))
            ->method('getVerbosity')
            ->will($this->returnValue(3));
        $helperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\HelperSet')
            ->getMock();

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);

        $this->assertTrue($consoleIO->isDebug());
        $this->assertFalse($consoleIO->isDebug());
    }

    public function testIsDecorated()
    {
        $inputMock = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock();
        $outputMock = $this->getMockBuilder('Symfony\Component\Console\Output\Output')
             ->setMethods(['isDecorated', 'doWrite'])
             ->getMock();
        $outputMock->expects($this->at(0))
            ->method('isDecorated')
            ->will($this->returnValue(true));
        $outputMock->expects($this->at(1))
            ->method('isDecorated')
            ->will($this->returnValue(false));
        $helperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\HelperSet')
            ->getMock();

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);

        $this->assertTrue($consoleIO->isDecorated());
        $this->assertFalse($consoleIO->isDecorated());
    }

    public function testWrite()
    {
        $inputMock = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock();
        $outputMock = $this->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
            ->getMock();
        $outputMock->expects($this->once())
            ->method('write')
            ->with($this->equalTo('Hi IO API'));
        $helperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\HelperSet')
            ->getMock();

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        $consoleIO->write('Hi IO API', false);
    }

    public function testAsk()
    {
        $inputMock = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock();
        $outputMock = $this->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
            ->getMock();
        $questionHelperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->getMock();
        $helperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\HelperSet')
            ->getMock();

        $question = new \Symfony\Component\Console\Question\Question('Your name?', '');

        $questionHelperMock->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf('Symfony\Component\Console\Input\InputInterface'),
                $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                $question);
        $helperMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('question'))
            ->will($this->returnValue($questionHelperMock));

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        $consoleIO->ask('Your name?', '');
    }

    public function testAskConfirmation()
    {
        $inputMock = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock();
        $outputMock = $this->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
            ->getMock();
        $questionHelperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->getMock();
        $helperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\HelperSet')
            ->getMock();

        $question = new \Symfony\Component\Console\Question\ConfirmationQuestion('Is valid?', true);

        $questionHelperMock->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf('Symfony\Component\Console\Input\InputInterface'),
                $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                $question);
        $helperMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('question'))
            ->will($this->returnValue($questionHelperMock));

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        $consoleIO->askConfirmation('Is valid?', true);
    }

    public function testAskAndValidate()
    {
        $inputMock = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock();
        $outputMock = $this->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
            ->getMock();
        $questionHelperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->getMock();
        $helperMock = $this->getMockBuilder('Symfony\Component\Console\Helper\HelperSet')
            ->getMock();

        $question = new \Symfony\Component\Console\Question\Question('Is valid?', true);
        $question->setMaxAttempts(10);
        $question->setValidator(function ($answer) {
            return $answer;
        });

        $questionHelperMock->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf('Symfony\Component\Console\Input\InputInterface'),
                $this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                $question);
        $helperMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('question'))
            ->will($this->returnValue($questionHelperMock));

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        $consoleIO->askAndValidate('Is valid?', function ($answer) {
            return $answer;
        }, 10, true);
    }
}
