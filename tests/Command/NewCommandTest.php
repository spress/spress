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
use Yosymfony\Spress\Command\NewCommand;

class NewCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {   
        $fs = new FileSystem();
        $fs->remove('./tests/out');
    }
    
    public function testNewSite()
    {
        $app = new Application();
        $app->add(new NewCommand());
        
        $command = $app->find('site:new');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => './tests/out'
        ]);
        
        $output = $commandTester->getDisplay();
        
        $this->assertRegExp('/New site created/', $output);
    }
    
    public function testNewSiteExistsEmptyDir()
    {
        $fs = new FileSystem();
        $fs->mkdir('./tests/out');
        
        $this->assertFileExists('./tests/out');
        
        $app = new Application();
        $app->add(new NewCommand());
        
        $command = $app->find('site:new');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => './tests/out'
        ]);
        
        $output = $commandTester->getDisplay();
        
        $this->assertRegExp('/New site created/', $output);
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testNewSiteNoForce()
    {
        $app = new Application();
        $app->add(new NewCommand());
        
        $command = $app->find('site:new');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => './tests/out',
        ]);
        
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => './tests/out',
        ]);
    }
    
    public function testNewSiteForce()
    {
        $app = new Application();
        $app->add(new NewCommand());
        
        $command = $app->find('site:new');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => './tests/out',
        ]);
        
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => './tests/out',
            '--force' => true,
        ]);
        
        $this->assertRegExp('/New site created/', $commandTester->getDisplay());
    }
    
    public function testNewSiteCompleteScaffold()
    {
        $app = new Application();
        $app->add(new NewCommand());
        
        $command = $app->find('site:new');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => './tests/out',
            '--all' => true,
        ]);
        
        $this->assertRegExp('/New site created/', $commandTester->getDisplay());
    }
}