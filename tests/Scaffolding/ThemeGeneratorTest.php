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
use Yosymfony\EmbeddedComposer\EmbeddedComposerBuilder;
use Yosymfony\Spress\IO\BufferIO;
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

    /**
     * @group net
     * @large
     */
    public function testSpressoTheme()
    {
        $autoloaders = spl_autoload_functions();
        $composerClassloader = $autoloaders[0][0];
        $builder = new EmbeddedComposerBuilder($composerClassloader, $this->tmpDir);
        $embeddedComposer = $builder->setComposerFilename('composer.json')
            ->setVendorDirectory('vendor')
            ->build();
        $embeddedComposer->processAdditionalAutoloads();

        $io = new BufferIO();
        $packageManager = new PackageManager($embeddedComposer, $io);

        $generator = new ThemeGenerator($packageManager);
        $generator->setSkeletonDirs($this->skeletonDir);
        $generator->generate($this->tmpDir, 'spress/spress-theme-spresso:2.1.*-dev');

        $this->assertFileExists($this->tmpDir.'/config.yml');
        $this->assertFileExists($this->tmpDir.'/composer.json');
        $this->assertFileNotExists($this->tmpDir.'/src/themes');
        $this->assertFileExists($this->tmpDir.'/src/content/index.html');
        $this->assertFileExists($this->tmpDir.'/src/content/posts');
        $this->assertFileExists($this->tmpDir.'/src/content/assets');
        $this->assertFileExists($this->tmpDir.'/src/layouts');
        $this->assertFileExists($this->tmpDir.'/src/includes');

        $this->assertRegExp('/Installing spress\/spress-theme-spresso/', $io->getOutput());
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage You must set the PackageManager at constructor in order to create non-blank themes.
     */
    public function testThemeAndNoPackageManagerSet()
    {
        $generator = new ThemeGenerator();
        $generator->setSkeletonDirs($this->skeletonDir);
        $generator->generate($this->tmpDir, 'spress/spress-theme-spresso:2.1.*-dev');
    }
}
