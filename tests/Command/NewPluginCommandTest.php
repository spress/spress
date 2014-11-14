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
use Yosymfony\Spress\Command\NewPluginCommand;

class NewPluginCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpDir;
    protected $currentDir;
    protected $fs;
    
    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir() . '/spress-tests';

        $this->fs = new Filesystem();
        $this->fs->mirror('./src/Yosymfony/Spress/Core/tests/fixtures/project', $this->tmpDir);

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
        $helper->setInputStream($this->getInputStream("yosymfony/testPlugin\n\nVictor <vpgugr@gmail.com>\nMy description\nMIT\n"));

        $commandTester->execute([
            'command' => $command->getName(),
        ]);
        
        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Spress plugin generator/', $output);
        $this->assertRegExp('/YosymfonytestPlugin.php/', $output);
        $this->assertRegExp('/composer.json/', $output);
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}