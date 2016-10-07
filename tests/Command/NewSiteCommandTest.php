<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Console\Application;
use Yosymfony\Spress\Command\NewSiteCommand;

class NewSiteCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $tmpDir;

    public function setUp()
    {
        $autoloaders = spl_autoload_functions();

        $this->app = new Application($autoloaders[0][0]);

        $this->tmpDir = sys_get_temp_dir().'/spress-tests';
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->tmpDir);
    }

    public function testNewSite()
    {
        $this->app->add(new NewSiteCommand());

        $command = $this->app->find('new:site');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
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

        $this->app->add(new NewSiteCommand());

        $command = $this->app->find('new:site');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => $this->tmpDir,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/New site created/', $output);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNewSiteNotForce()
    {
        $this->app->add(new NewSiteCommand());

        $command = $this->app->find('new:site');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => $this->tmpDir,
        ]);

        $commandTester->execute([
            'path' => $this->tmpDir,
        ]);
    }

    public function testNewSiteForce()
    {
        $this->app->add(new NewSiteCommand());

        $command = $this->app->find('new:site');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
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
        $this->app->add(new NewSiteCommand());

        $command = $this->app->find('new:site');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => $this->tmpDir,
            '--all' => true,
        ]);

        $this->assertRegExp('/New site created/', $commandTester->getDisplay());
    }
}
