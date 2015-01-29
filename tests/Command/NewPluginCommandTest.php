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

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Command\NewPluginCommand;

class NewPluginCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpDir;
    protected $currentDir;
    protected $fs;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir().'/spress-tests';

        $this->fs = new Filesystem();
        $this->fs->mirror('./src/Core/tests/fixtures/project', $this->tmpDir);

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
        $app = new Application();
        $app->add(new NewPluginCommand());

        $command = $app->find('new:plugin');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("yosymfony/testplugin\n\nVictor\nvpgugr@gmail.com\nMy description\nMIT\n"));

        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Spress plugin generator/', $output);
        $this->assertRegExp('/Yosymfonytestplugin\.php/', $output);
        $this->assertRegExp('/composer\.json/', $output);
    }

    public function testOnlyName()
    {
        $app = new Application();
        $app->add(new NewPluginCommand());

        $command = $app->find('new:plugin');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("yosymfony/testplugin\n\n\n\n\n"));

        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Spress plugin generator/', $output);
        $this->assertRegExp('/Yosymfonytestplugin\.php/', $output);
        $this->assertRegExp('/composer\.json/', $output);
        $this->assertRegExp('/LICENSE/', $output);

        $fileContent = file_get_contents($this->tmpDir.'/_plugins/Yosymfonytestplugin/Yosymfonytestplugin.php');

        $this->assertNotRegExp('/namespace/', $fileContent);

        $fileContent = file_get_contents($this->tmpDir.'/_plugins/Yosymfonytestplugin/composer.json');

        $this->assertNotRegExp('/"psr-4":/', $fileContent);

        $fileContent = file_get_contents($this->tmpDir.'/_plugins/Yosymfonytestplugin/LICENSE');

        $this->assertNotRegExp('/The MIT License (MIT)/', $fileContent);
    }

    public function testDefaultValues()
    {
        $app = new Application();
        $app->add(new NewPluginCommand());

        $command = $app->find('new:plugin');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("\n\n\n\n\n\n"));

        $commandTester->execute([
            'command'       => $command->getName(),
            '--name'        => 'yosymfony/testplugin',
            '--author'      => 'Victor Puertas',
            '--email'       => 'vpgugr@gmail.com',
            '--description' => 'My Spress plugin',
            '--license'     => 'BSD-2-Clause',
        ]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Spress plugin generator/', $output);
        $this->assertRegExp('/Yosymfonytestplugin\.php/', $output);
        $this->assertRegExp('/composer\.json/', $output);
        $this->assertRegExp('/LICENSE/', $output);

        $fileContent = file_get_contents($this->tmpDir.'/_plugins/Yosymfonytestplugin/Yosymfonytestplugin.php');

        $this->assertRegExp('/class Yosymfonytestplugin/', $fileContent);

        $fileContent = file_get_contents($this->tmpDir.'/_plugins/Yosymfonytestplugin/composer.json');

        $this->assertRegExp('/"name": "yosymfony\/testplugin"/', $fileContent);
        $this->assertRegExp('/"description": "My Spress plugin"/', $fileContent);
        $this->assertRegExp('/"name": "Victor Puertas"/', $fileContent);
        $this->assertRegExp('/"email": "vpgugr@gmail.com"/', $fileContent);
        $this->assertRegExp('/"license": "BSD-2-Clause"/', $fileContent);
        $this->assertRegExp('/"spress_name": "Yosymfonytestplugin"/', $fileContent);
    }

    public function testOnlyAuthorName()
    {
        $app = new Application();
        $app->add(new NewPluginCommand());

        $command = $app->find('new:plugin');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("\n\n\n\n\n\n"));

        $commandTester->execute([
            'command'       => $command->getName(),
            '--name'        => 'yosymfony/testplugin',
            '--author'      => 'Victor Puertas',
        ]);

        $output = $commandTester->getDisplay();

        $fileContent = file_get_contents($this->tmpDir.'/_plugins/Yosymfonytestplugin/composer.json');

        $this->assertNotRegExp('/"authors":/', $fileContent);
    }

    public function testOnlyEmailAuthor()
    {
        $app = new Application();
        $app->add(new NewPluginCommand());

        $command = $app->find('new:plugin');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("\n\n\n\n\n\n"));

        $commandTester->execute([
            'command'       => $command->getName(),
            '--name'        => 'yosymfony/testplugin',
            '--email'       => 'vpgugr@gmail.com',
        ]);

        $output = $commandTester->getDisplay();

        $fileContent = file_get_contents($this->tmpDir.'/_plugins/Yosymfonytestplugin/composer.json');

        $this->assertNotRegExp('/"authors":/', $fileContent);
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
