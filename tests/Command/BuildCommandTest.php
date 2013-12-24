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

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Yosymfony\Spress\Command\BuildCommand;

class BuildCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildCommand()
    {
        $app = new Application();
        $app->add(new BuildCommand());
        
        $command = $app->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--source' => './tests/fixtures/project'
        ]);
        
        $output = $commandTester->getDisplay();
        
        $this->assertRegExp('/Starting.../', $output);
        $this->assertRegExp('/Total post/', $output);
    }
    
    public function testBuildCommandDraft()
    {
        $app = new Application();
        $app->add(new BuildCommand());
        
        $command = $app->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--source' => './tests/fixtures/project',
            '--drafts' => true
        ]);
        
        $output = $commandTester->getDisplay();
        
        $this->assertRegExp('/Starting.../', $output);
        $this->assertRegExp('/With posts drafts active/', $output);
        $this->assertRegExp('/Total post/', $output);
    }
    
    public function testBuildCommandSafe()
    {
        $app = new Application();
        $app->add(new BuildCommand());
        
        $command = $app->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--source' => './tests/fixtures/project',
            '--safe' => true
        ]);
        
        $output = $commandTester->getDisplay();
        
        $this->assertRegExp('/Starting.../', $output);
        $this->assertRegExp('/Plugins disabled/', $output);
    }
}