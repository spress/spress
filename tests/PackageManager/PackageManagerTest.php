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
use Composer\Package\Package;
use Composer\Repository\RepositoryManager;
use Yosymfony\EmbeddedComposer\EmbeddedComposer;
use Yosymfony\Spress\Core\IO\NullIO;
use Yosymfony\Spress\PackageManager\PackageManager;

class PackageManagerTest extends \PHPUnit_Framework_TestCase
{
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
}
