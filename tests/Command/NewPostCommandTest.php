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
use Yosymfony\Spress\Command\NewPostCommand;

class NewPostCommandTest extends \PHPUnit_Framework_TestCase
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

    public function testNewPost()
    {
        $app = new Application();
        $app->add(new NewPostCommand());
        
        $command = $app->find('new:post');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("My first post\n\n\ntag1 tag2\ncategory1 category2\n"));

        $commandTester->execute([
            'command' => $command->getName(),
        ]);
        
        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Spress post generator/', $output);
        $this->assertRegExp('/my-first-post.md/', $output);
    }

    public function testOnlyTitle()
    {
        $app = new Application();
        $app->add(new NewPostCommand());
        
        $command = $app->find('new:post');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("My first post\n\n\n\n\n"));

        $commandTester->execute([
            'command' => $command->getName(),
        ]);
        
        $output = $commandTester->getDisplay();

        $this->assertRegExp('/my-first-post.md/', $output);
    }

    public function testWithDate()
    {
        $app = new Application();
        $app->add(new NewPostCommand());
        
        $command = $app->find('new:post');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("My first post\n\n2014-01-01\n\n\n"));

        $commandTester->execute([
            'command' => $command->getName(),
        ]);
        
        $output = $commandTester->getDisplay();

        $this->assertRegExp('/2014-01-01-my-first-post.md/', $output);
    }

    public function testDefaultValues()
    {
        $app = new Application();
        $app->add(new NewPostCommand());
        
        $command = $app->find('new:post');
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("\n\n\n\n\n"));

        $commandTester->execute([
            'command'       => $command->getName(),
            '--title'         => 'My second post',
            '--layout'        => 'post',
            '--date'          => '2015-01-01',
            '--tags'          => 'tag1 tag2',
            '--categories'    => 'category1 category2',
        ]);
        
        $output = $commandTester->getDisplay();

        $this->assertRegExp('/2015-01-01-my-second-post.md/', $output);
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
