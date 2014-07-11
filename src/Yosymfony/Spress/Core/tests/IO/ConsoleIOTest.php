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
 
namespace Yosymfony\Spress\Core\Tests\IO;

use Yosymfony\Spress\Core\IO\ConsoleIO;

class ConsoleIOTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInteractive()
    {
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $inputMock->expects($this->at(0))
            ->method('isInteractive')
            ->will($this->returnValue(true));
        $inputMock->expects($this->at(1))
            ->method('isInteractive')
            ->will($this->returnValue(false));
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);

        $this->assertTrue($consoleIO->isInteractive());
        $this->assertFalse($consoleIO->isInteractive());
    }
    
    public function testIsVerbose()
    {
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $outputMock->expects($this->at(0))
            ->method('getVerbosity')
            ->will($this->returnValue(2));
        $outputMock->expects($this->at(1))
            ->method('getVerbosity')
            ->will($this->returnValue(0));
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        
        $this->assertTrue($consoleIO->isVerbose());
        $this->assertFalse($consoleIO->isVerbose());
    }
    
    public function testIsVeryVerbose()
    {
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $outputMock->expects($this->at(0))
            ->method('getVerbosity')
            ->will($this->returnValue(3));
        $outputMock->expects($this->at(1))
            ->method('getVerbosity')
            ->will($this->returnValue(2));
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        
        $this->assertTrue($consoleIO->isVeryVerbose());
        $this->assertFalse($consoleIO->isVeryVerbose());
    }
    
    public function testIsDebug()
    {
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $outputMock->expects($this->at(0))
            ->method('getVerbosity')
            ->will($this->returnValue(4));
        $outputMock->expects($this->at(1))
            ->method('getVerbosity')
            ->will($this->returnValue(3));
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        
        $this->assertTrue($consoleIO->isDebug());
        $this->assertFalse($consoleIO->isDebug());
    }
    
    public function testIsDecorated()
    {
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $outputMock->expects($this->at(0))
            ->method('isDecorated')
            ->will($this->returnValue(true));
        $outputMock->expects($this->at(1))
            ->method('isDecorated')
            ->will($this->returnValue(false));
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        
        $this->assertTrue($consoleIO->isDecorated());
        $this->assertFalse($consoleIO->isDecorated());
    }
    
    public function testWrite()
    {
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $outputMock->expects($this->once())
            ->method('write')
            ->with($this->equalTo('Hi IO API'));
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');
        
        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        $consoleIO->write('Hi IO API', false);
    }
    
    public function testAsk()
    {
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $dialogMock = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');
        
        $dialogMock->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                $this->equalTo('Is valid?'),
                $this->equalTo('default'));
        $helperMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('dialog'))
            ->will($this->returnValue($dialogMock));
        
        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        $consoleIO->ask('Is valid?', 'default');
    }
    
    public function testAskConfirmation()
    {
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $dialogMock = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');

        $dialogMock->expects($this->once())
            ->method('askConfirmation')
            ->with($this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                $this->equalTo('Is valid?'),
                $this->equalTo('default'));
        $helperMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('dialog'))
            ->will($this->returnValue($dialogMock));

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        $consoleIO->askConfirmation('Is valid?', 'default');
    }
    
    public function testAskAndValidate()
    {
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $dialogMock = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');

        $dialogMock->expects($this->once())
            ->method('askAndValidate')
            ->with($this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'),
                $this->equalTo('Is valid?'),
                $this->callback(function($validator) {
                    return 'validator' === $validator();
                }),
                $this->equalTo(10),
                $this->equalTo('default'));
        $helperMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('dialog'))
            ->will($this->returnValue($dialogMock));

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        $consoleIO->askAndValidate('Is valid?', function() { 
            return 'validator';
        }, 10, 'default');
    }
}