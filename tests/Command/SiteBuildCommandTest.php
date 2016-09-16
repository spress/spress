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

use Yosymfony\Spress\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class SiteBuildCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $sourceDir;

    public function setUp()
    {
        $autoloaders = spl_autoload_functions();

        $this->app = new Application($autoloaders[0][0]);
        $this->app->registerStandardCommands();

        $this->sourceDir = __DIR__.'/../../src/Core/Tests/fixtures/project';
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->sourceDir.'/build');
        $fs->remove($this->sourceDir.'/config_test.yml');
    }

    public function testBuildCommand()
    {
        $command = $this->app->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--source' => $this->sourceDir,
        ], ['decorated' => false]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Starting.../', $output);
        $this->assertRegExp('/Environment: dev/', $output);
        $this->assertNotRegExp('/Posts drafts enabled/', $output);
        $this->assertRegExp('/Debug mode: enabled/', $output);
        $this->assertRegExp('/Total items: 18/', $output);
    }

    public function testBuildCommandDraft()
    {
        $command = $this->app->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--source' => $this->sourceDir,
            '--drafts' => true,
        ], ['decorated' => false]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Starting.../', $output);
        $this->assertRegExp('/Environment: dev/', $output);
        $this->assertRegExp('/Draft posts: enabled/', $output);
        $this->assertRegExp('/Total items: 18/', $output);
    }

    public function testBuildCommandSafe()
    {
        $command = $this->app->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--source' => $this->sourceDir,
            '--safe' => true,
        ], ['decorated' => false]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Starting.../', $output);
        $this->assertRegExp('/Environment: dev/', $output);
        $this->assertRegExp('/Plugins: disabled/', $output);
    }

    public function testBuildCommandEnv()
    {
        $command = $this->app->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--source' => $this->sourceDir,
            '--env' => 'prod',
        ], ['decorated' => false]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Starting.../', $output);
        $this->assertRegExp('/Environment: prod/', $output);
    }

    public function testParsedownActived()
    {
        $fs = new Filesystem();
        $fs->dumpFile($this->sourceDir.'/config_test.yml', 'parsedown_activated: true');

        $command = $this->app->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--source' => $this->sourceDir,
            '--env' => 'test',
        ], ['decorated' => false]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Parsedown converter: enabled/', $output);
    }
}
