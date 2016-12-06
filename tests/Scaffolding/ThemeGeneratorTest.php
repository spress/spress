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
use Yosymfony\Spress\PackageManager\PackageManager;
use Yosymfony\Spress\Scaffolding\ThemeGenerator;

class ThemeGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $skeletonDir;
    protected $tmpDir;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir().'/spress-tests';
        $this->skeletonDir = [__DIR__.'/../../app/skeletons'];
    }

    public function tearDown()
    {
        $fs = new FileSystem();
        $fs->remove($this->tmpDir);
    }

    public function testBlankTheme()
    {
        $generator = new ThemeGenerator();
        $generator->setSkeletonDirs($this->skeletonDir);
        $generator->generate($this->tmpDir, 'blank');

        $this->assertFileExists($this->tmpDir.'/config.yml');
        $this->assertFileExists($this->tmpDir.'/composer.json');
        $this->assertFileExists($this->tmpDir.'/src/content/index.html');
        $this->assertFileExists($this->tmpDir.'/src/content/posts');
        $this->assertFileExists($this->tmpDir.'/src/layouts');
        $this->assertFileExists($this->tmpDir.'/src/includes');
        $this->assertFileExists($this->tmpDir.'/src/plugins');
    }

    public function testTheme()
    {
        $packageManagerMock = $this->getMockBuilder(PackageManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $packageManagerMock
            ->expects($this->once())
            ->method('createThemeProject')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->equalTo(false)
            );

        $generator = new ThemeGenerator($packageManagerMock);
        $generator->setSkeletonDirs($this->skeletonDir);
        $generator->generate($this->tmpDir, 'spress/spress-theme-spresso:2.1.*-dev');
    }

    public function testThemeWithRepository()
    {
        $packageManagerMock = $this->getMockBuilder(PackageManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $packageManagerMock
            ->expects($this->once())
            ->method('createThemeProject')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->equalTo('http://repository.foo.com')
        );

        $generator = new ThemeGenerator($packageManagerMock);
        $generator->setSkeletonDirs($this->skeletonDir);
        $generator->generate(
            $this->tmpDir,
            'spress/spress-theme-spresso:2.1.*-dev',
            false,
            [
                'repository' => 'http://repository.foo.com',
            ]
        );
    }

    public function testThemePreferSource()
    {
        $packageManagerMock = $this->getMockBuilder(PackageManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $packageManagerMock
            ->expects($this->once())
            ->method('createThemeProject')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->equalTo(true)
            );

        $generator = new ThemeGenerator($packageManagerMock);
        $generator->setSkeletonDirs($this->skeletonDir);
        $generator->generate(
            $this->tmpDir,
            'spress/spress-theme-spresso:2.1.*-dev',
            false,
            [
                'prefer-source' => true,
            ]
        );
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage You must set the PackageManager at the constructor in order to create non-blank themes.
     */
    public function testThemeAndNoPackageManagerSet()
    {
        $generator = new ThemeGenerator();
        $generator->setSkeletonDirs($this->skeletonDir);
        $generator->generate($this->tmpDir, 'spress/spress-theme-spresso:2.1.*-dev');
    }
}
