<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Tests;

use Yosymfony\Spress\IO\ConsoleIO;

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
    
    public function isVeryVerbose()
    {
        $inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $outputMock->expects($this->at(0))
            ->method('getVerbosity')
            ->will($this->returnValue(3));
        $helperMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');

        $consoleIO = new ConsoleIO($inputMock, $outputMock, $helperMock);
        
        $this->assertTrue($consoleIO->isVeryVerbose());
    }
}