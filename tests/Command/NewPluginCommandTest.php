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
use Yosymfony\Spress\Command\NewPluginCommand;

class NewPluginCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $tmpDir;
    protected $currentDir;
    protected $fs;

    public function setUp()
    {
        $autoloaders = spl_autoload_functions();

        $this->app = new Application($autoloaders[0][0]);

        $this->tmpDir = sys_get_temp_dir().'/spress-tests';

        $this->fs = new Filesystem();
        $this->fs->mirror(__DIR__.'/../../src/Core/Tests/fixtures/project', $this->tmpDir);

        $this->currentDir = getcwd();

        chdir($this->tmpDir);
    }

    public function tearDown()
    {
        chdir($this->currentDir);

        $this->fs->remove($this->tmpDir);
    }

    public function testNewPlugin()
    {
        $this->app->add(new NewPluginCommand());

        $command = $this->app->find('new:plugin');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--name' => 'yosymfony/testplugin',
            '--author' => 'Victor Puertas',
            '--email' => 'vpgugr@gmail.com',
            '--description' => 'My Spress plugin',
            '--license' => 'BSD-2-Clause',
        ], ['interactive' => false]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/YosymfonyTestplugin\.php/', $output);
        $this->assertRegExp('/composer\.json/', $output);
        $this->assertRegExp('/LICENSE/', $output);

        $fileContent = file_get_contents($this->tmpDir.'/src/plugins/YosymfonyTestplugin/YosymfonyTestplugin.php');

        $this->assertRegExp('/class YosymfonyTestplugin/', $fileContent);
        $this->assertRegExp('/implements PluginInterface/', $fileContent);

        $fileContent = file_get_contents($this->tmpDir.'/src/plugins/YosymfonyTestplugin/composer.json');

        $this->assertRegExp('/"name": "yosymfony\/testplugin"/', $fileContent);
        $this->assertRegExp('/"description": "My Spress plugin"/', $fileContent);
        $this->assertRegExp('/"name": "Victor Puertas"/', $fileContent);
        $this->assertRegExp('/"email": "vpgugr@gmail.com"/', $fileContent);
        $this->assertRegExp('/"license": "BSD-2-Clause"/', $fileContent);
    }

    public function testNewCommandPlugin()
    {
        $this->app->add(new NewPluginCommand());

        $command = $this->app->find('new:plugin');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--name' => 'yosymfony/testplugin',
            '--command-name' => 'Accme',
            '--command-description' => 'Short description',
            '--command-help' => 'Help of the command',
            '--author' => 'Victor Puertas',
            '--email' => 'vpgugr@gmail.com',
            '--description' => 'My Spress plugin',
            '--license' => 'BSD-2-Clause',
        ], ['interactive' => false]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/YosymfonyTestplugin\.php/', $output);
        $this->assertRegExp('/composer\.json/', $output);
        $this->assertRegExp('/LICENSE/', $output);

        $fileContent = file_get_contents($this->tmpDir.'/src/plugins/YosymfonyTestplugin/YosymfonyTestplugin.php');

        $this->assertRegExp('/class YosymfonyTestplugin/', $fileContent);
        $this->assertRegExp('/extends CommandPlugin/', $fileContent);

        $fileContent = file_get_contents($this->tmpDir.'/src/plugins/YosymfonyTestplugin/composer.json');

        $this->assertRegExp('/"name": "yosymfony\/testplugin"/', $fileContent);
        $this->assertRegExp('/"description": "My Spress plugin"/', $fileContent);
        $this->assertRegExp('/"name": "Victor Puertas"/', $fileContent);
        $this->assertRegExp('/"email": "vpgugr@gmail.com"/', $fileContent);
        $this->assertRegExp('/"license": "BSD-2-Clause"/', $fileContent);
    }
}
