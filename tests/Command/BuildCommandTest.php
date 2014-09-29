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
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Command\BuildCommand;

class BuildCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $sourceDir;
    
    public function setUp()
    {
        $this->sourceDir = './src/Yosymfony/Spress/Core/tests/fixtures/project';
    }
    
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->sourceDir . '/_site');
    }
    
    public function testBuildCommand()
    {
        $app = new Application();
        $app->add(new BuildCommand());
        
        $command = $app->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--source' => $this->sourceDir,
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
            '--source' => $this->sourceDir,
            '--drafts' => true,
        ]);
        
        $output = $commandTester->getDisplay();
        
        $this->assertRegExp('/Starting.../', $output);
        $this->assertRegExp('/Posts drafts enabled/', $output);
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
            '--source' => $this->sourceDir,
            '--safe' => true,
        ]);
        
        $output = $commandTester->getDisplay();
        
        $this->assertRegExp('/Starting.../', $output);
        $this->assertRegExp('/Plugins disabled/', $output);
    }
    
    public function testBuildCommandEnv()
    {
        $app = new Application();
        $app->add(new BuildCommand());
        
        $command = $app->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--source' => $this->sourceDir,
            '--env' => 'prod',
        ]);
        
        $output = $commandTester->getDisplay();
        
        $this->assertRegExp('/Starting.../', $output);
        $this->assertRegExp('/Environment: prod/', $output);
    }
}