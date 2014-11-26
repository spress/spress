<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\tests\Scaffolding;

use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Scaffolding\PluginGenerator;

class PluginGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpDir;
    protected $skeletonDir;

    public function setUp()
    {
        $this->skeletonDir = './app/skeletons';
        $this->tmpDir = sys_get_temp_dir().'/spress-tests';
    }

    public function tearDown()
    {
        $fs = new FileSystem();
        $fs->remove($this->tmpDir);
    }

    public function testGenerate()
    {
        $generator = new PluginGenerator();
        $generator->setSkeletonDirs($this->skeletonDir);
        $files = $generator->generate($this->tmpDir, 'yosymfony/myplugin');

        $this->assertCount(3, $files);
        $this->assertFileExists($files[0]);
        $this->assertFileExists($files[1]);
        $this->assertRegExp('/Yosymfonymyplugin.php/', $files[0]);
        $this->assertRegExp('/composer.json/', $files[1]);
        $this->assertRegExp('/LICENSE/', $files[2]);
    }

    public function testLicenseNotExists()
    {
        $generator = new PluginGenerator();
        $generator->setSkeletonDirs($this->skeletonDir);
        $files = $generator->generate($this->tmpDir, 'yosymfony/myplugin', '', '', '', '', 'My-license');

        $this->assertCount(2, $files);
        $this->assertFileExists($files[0]);
        $this->assertFileExists($files[1]);
        $this->assertRegExp('/Yosymfonymyplugin.php/', $files[0]);
        $this->assertRegExp('/composer.json/', $files[1]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNameEmpty()
    {
        $generator = new PluginGenerator();
        $generator->setSkeletonDirs($this->skeletonDir);
        $files = $generator->generate($this->tmpDir, '');
    }
}
