<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Command\NewCommand;

class NewCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpDir;
    
    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir() . '/spress-tests';
    }
    
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->tmpDir);
    }
    
    public function testNewSite()
    {
        $app = new Application();
        $app->add(new NewCommand());
        
        $command = $app->find('site:new');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => $this->tmpDir,
        ]);
        
        $output = $commandTester->getDisplay();
        
        $this->assertRegExp('/New site created/', $output);
    }
    
    public function testNewSiteExistsEmptyDir()
    {
        $fs = new FileSystem();
        $fs->mkdir($this->tmpDir);
        
        $this->assertFileExists($this->tmpDir);
        
        $app = new Application();
        $app->add(new NewCommand());
        
        $command = $app->find('site:new');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => $this->tmpDir,
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
            'path' => $this->tmpDir,
        ]);
        
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => $this->tmpDir,
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
            'path' => $this->tmpDir,
        ]);
        
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => $this->tmpDir,
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
            'path' => $this->tmpDir,
            '--all' => true,
        ]);
        
        $this->assertRegExp('/New site created/', $commandTester->getDisplay());
    }
}