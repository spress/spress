<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Test\PackageManager;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Repository\RepositoryManager;
use Composer\Semver\Constraint\EmptyConstraint;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\EmbeddedComposer\EmbeddedComposer;
use Yosymfony\EmbeddedComposer\EmbeddedComposerBuilder;
use Yosymfony\Spress\Core\IO\NullIO;
use Yosymfony\Spress\IO\BufferIO;
use Yosymfony\Spress\PackageManager\PackageManager;

class PackageManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $tmpDir;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir().'/spress-tests';
    }

    public function tearDown()
    {
        $fs = new FileSystem();
        $fs->remove($this->tmpDir);
    }

    public function testGetRootDirectory()
    {
        $embeddedComposerStub = $this->getMockBuilder(EmbeddedComposer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $embeddedComposerStub->method('getExternalRootDirectory')
            ->willReturn('foo');

        $packageManager = new PackageManager($embeddedComposerStub, new NullIO());

        $this->assertEquals('foo', $packageManager->getRootDirectory());
    }

    public function testExistPackage()
    {
        $composer = new Composer();
        $managerStub = $this->getMockBuilder(RepositoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $managerStub->method('findPackage')
            ->willReturn(new Package('foo', '1.0.0', '1.0'));

        $composer->setRepositoryManager($managerStub);

        $embeddedComposerStub = $this->getMockBuilder(EmbeddedComposer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $embeddedComposerStub->method('createComposer')
            ->willReturn($composer);
        $packageManager = new PackageManager($embeddedComposerStub, new NullIO());

        $this->assertTrue($packageManager->existPackage('foo:1.0.0'));
    }

    public function testNotExistPackage()
    {
        $composer = new Composer();
        $managerStub = $this->getMockBuilder(RepositoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $managerStub->method('findPackage')
            ->willReturn(null);

        $composer->setRepositoryManager($managerStub);

        $embeddedComposerStub = $this->getMockBuilder(EmbeddedComposer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $embeddedComposerStub->method('createComposer')
            ->willReturn($composer);
        $packageManager = new PackageManager($embeddedComposerStub, new NullIO());

        $this->assertFalse($packageManager->existPackage('foo:1.0.0'));
    }

    public function testIsThemePackage()
    {
        $composer = new Composer();
        $package = new Package('foo', '1.0.0', '1.0');
        $package->setType(PackageManager::PACKAGE_TYPE_THEME);

        $managerStub = $this->getMockBuilder(RepositoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $managerStub->method('findPackage')
            ->willReturn($package);

        $composer->setRepositoryManager($managerStub);

        $embeddedComposerStub = $this->getMockBuilder(EmbeddedComposer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $embeddedComposerStub->method('createComposer')
            ->willReturn($composer);
        $packageManager = new PackageManager($embeddedComposerStub, new NullIO());

        $this->assertTrue($packageManager->isThemePackage('foo:1.0.0'));
    }

    public function testIsNotThemePackage()
    {
        $composer = new Composer();
        $package = new Package('foo', '1.0.0', '1.0');

        $managerStub = $this->getMockBuilder(RepositoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $managerStub->method('findPackage')
            ->willReturn($package);

        $composer->setRepositoryManager($managerStub);

        $embeddedComposerStub = $this->getMockBuilder(EmbeddedComposer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $embeddedComposerStub->method('createComposer')
            ->willReturn($composer);
        $packageManager = new PackageManager($embeddedComposerStub, new NullIO());

        $this->assertFalse($packageManager->isThemePackage('foo:1.0.0'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage The theme: "foo:1.0.0" does not exist.
     */
    public function testThemePackageNotFound()
    {
        $composer = new Composer();
        $managerStub = $this->getMockBuilder(RepositoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $managerStub->method('findPackage')
            ->willReturn(null);

        $composer->setRepositoryManager($managerStub);

        $embeddedComposerStub = $this->getMockBuilder(EmbeddedComposer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $embeddedComposerStub->method('createComposer')
            ->willReturn($composer);
        $packageManager = new PackageManager($embeddedComposerStub, new NullIO());

        $this->assertFalse($packageManager->isThemePackage('foo:1.0.0'));
    }

    public function testIsPackageDependOn()
    {
        $composer = new Composer();
        $package = new Package('foo', '1.0.0', '1.0');
        $package->setRequires([new Link('foo', 'foo-b', new EmptyConstraint())]);

        $managerStub = $this->getMockBuilder(RepositoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $managerStub->method('findPackage')
            ->willReturn($package);

        $composer->setRepositoryManager($managerStub);

        $embeddedComposerStub = $this->getMockBuilder(EmbeddedComposer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $embeddedComposerStub->method('createComposer')
            ->willReturn($composer);
        $packageManager = new PackageManager($embeddedComposerStub, new NullIO());

        $this->assertTrue($packageManager->isPackageDependOn('foo:1.0.0', 'foo-b: 2.0.0'));
    }

    public function testIsPackageNotDependOn()
    {
        $composer = new Composer();
        $package = new Package('foo', '1.0.0', '1.0');
        $package->setRequires([new Link('foo', 'foo-c', new EmptyConstraint())]);

        $managerStub = $this->getMockBuilder(RepositoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $managerStub->method('findPackage')
            ->willReturn($package);

        $composer->setRepositoryManager($managerStub);

        $embeddedComposerStub = $this->getMockBuilder(EmbeddedComposer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $embeddedComposerStub->method('createComposer')
            ->willReturn($composer);
        $packageManager = new PackageManager($embeddedComposerStub, new NullIO());

        $this->assertFalse($packageManager->isPackageDependOn('foo:1.0.0', 'foo-b: 2.0.0'));
    }

    /**
     * @group net
     * @large
     */
    public function testCreateThemeProject()
    {
        $autoloaders = spl_autoload_functions();
        $composerClassloader = $autoloaders[0][0];
        $builder = new EmbeddedComposerBuilder($composerClassloader, $this->tmpDir);
        $embeddedComposer = $builder->setComposerFilename('composer.json')
            ->setVendorDirectory('vendor')
            ->build();

        $io = new BufferIO();
        $packageManager = new PackageManager($embeddedComposer, $io);
        $packageManager->createThemeProject($this->tmpDir, 'spress/spress-theme-spresso:2.1.*-dev');

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
     * @group net
     * @large
     */
    public function testCreateThemeProjectPreferSource()
    {
        $autoloaders = spl_autoload_functions();
        $composerClassloader = $autoloaders[0][0];
        $builder = new EmbeddedComposerBuilder($composerClassloader, $this->tmpDir);
        $embeddedComposer = $builder->setComposerFilename('composer.json')
            ->setVendorDirectory('vendor')
            ->build();

        $io = new BufferIO();
        $packageManager = new PackageManager($embeddedComposer, $io);
        $packageManager->createThemeProject(
            $this->tmpDir,
            'spress/spress-theme-spresso:2.1.*-dev',
            null,
            true
        );

        $this->assertFileExists($this->tmpDir.'/config.yml');
        $this->assertFileExists($this->tmpDir.'/composer.json');
        $this->assertFileNotExists($this->tmpDir.'/src/themes');
        $this->assertFileExists($this->tmpDir.'/src/content/index.html');
        $this->assertFileExists($this->tmpDir.'/src/content/posts');
        $this->assertFileExists($this->tmpDir.'/src/content/assets');
        $this->assertFileExists($this->tmpDir.'/src/layouts');
        $this->assertFileExists($this->tmpDir.'/src/includes');
        $this->assertFileNotExists($this->tmpDir.'/.git');

        $this->assertRegExp('/Installing spress\/spress-theme-spresso/', $io->getOutput());
    }

    public function testAddPackage()
    {
        $embeddedComposerStub = $this->getMockBuilder(EmbeddedComposer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $embeddedComposerStub->method('getExternalComposerFilename')
            ->willReturn($this->tmpDir.'/composer.json');
        $packageManager = new PackageManager($embeddedComposerStub, new NullIO());
        $packages = $packageManager->addPackage([
            'spress/spress-theme-spresso:2.1.*-dev',
        ]);

        $this->assertArrayHasKey('spress/spress-theme-spresso', $packages);
    }
}
