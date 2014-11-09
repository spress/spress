<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Tests\Scaffolding;

use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Scaffolding\PostGenerator;

class PostGeneratorTest extends \PHPUnit_Framework_TestCase
{
	protected $tmpDir;
    protected $skeletonDir;
    
    public function setUp()
    {
        $this->skeletonDir = './app/skeletons';
        $this->tmpDir = sys_get_temp_dir() . '/spress-tests';
    }

    public function tearDown()
    {
        $fs = new FileSystem();
        $fs->remove($this->tmpDir);
    }

    public function testGenerate()
    {
    	$generator = new PostGenerator();
    	$generator->setSkeletonDirs($this->skeletonDir);
    	$files = $generator->generate($this->tmpDir, new \DateTime(), 'My first post', 'default', [], []);

        $this->assertCount(1, $files);
        $this->assertFileExists($files[0]);
        $this->assertRegExp('/my-first-post/', $files[0]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTitleEmpty()
    {
        $generator = new PostGenerator();
        $generator->setSkeletonDirs($this->skeletonDir);
        $files = $generator->generate($this->tmpDir, new \DateTime(), '', 'default', [], []);
    }
}
