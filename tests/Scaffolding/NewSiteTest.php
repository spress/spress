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
use Yosymfony\Spress\Scaffolding\NewSite;

class NewSiteTest extends \PHPUnit_Framework_TestCase
{
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

    public function testNewSiteBlank()
    {
        $operation = new NewSite();
        $operation->newSite($this->tmpDir, 'blank');

        $this->assertFileExists($this->tmpDir.'/config.yml');
        $this->assertFileExists($this->tmpDir.'/composer.json');
        $this->assertFileExists($this->tmpDir.'/src/content/index.html');
        $this->assertFileExists($this->tmpDir.'/src/content/posts');
        $this->assertFileExists($this->tmpDir.'/src/layouts');
    }

    public function testNewSiteExistsEmptyDir()
    {
        $fs = new FileSystem();
        $fs->mkdir($this->tmpDir);

        $this->assertFileExists($this->tmpDir);

        $operation = new NewSite();
        $operation->newSite($this->tmpDir, 'blank');

        $this->assertFileExists($this->tmpDir.'/config.yml');
        $this->assertFileExists($this->tmpDir.'/composer.json');
        $this->assertFileExists($this->tmpDir.'/src/content/index.html');
        $this->assertFileExists($this->tmpDir.'/src/content/posts');
        $this->assertFileExists($this->tmpDir.'/src/layouts');
    }

    public function testNewSiteBlankForce()
    {
        $operation = new NewSite();
        $operation->newSite($this->tmpDir, 'blank');
        $operation->newSite($this->tmpDir, 'blank', true);

        $this->assertFileExists($this->tmpDir.'/config.yml');
        $this->assertFileExists($this->tmpDir.'/composer.json');
        $this->assertFileExists($this->tmpDir.'/src/content/index.html');
        $this->assertFileExists($this->tmpDir.'/src/content/posts');
        $this->assertFileExists($this->tmpDir.'/src/layouts');
    }

    public function testNewSiteBlankCompleteScaffold()
    {
        $operation = new NewSite();
        $operation->newSite($this->tmpDir, 'blank', false, true);

        $this->assertFileExists($this->tmpDir.'/config.yml');
        $this->assertFileExists($this->tmpDir.'/composer.json');
        $this->assertFileExists($this->tmpDir.'/src/content/index.html');
        $this->assertFileExists($this->tmpDir.'/src/content/posts');
        $this->assertFileExists($this->tmpDir.'/src/layouts');
        $this->assertFileExists($this->tmpDir.'/src/includes');
        $this->assertFileExists($this->tmpDir.'/src/plugins');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage You must set the PackageManager at constructor in order to create non-blank themes.
     */
    public function testNewSiteWithTemplateAndNoPackageManagerTest()
    {
        $operation = new NewSite();
        $operation->newSite($this->tmpDir, 'template-test');

        $this->assertFileExists($this->tmpDir.'/config.yml');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNewSiteBlankNoForce()
    {
        $operation = new NewSite();
        $operation->newSite($this->tmpDir, 'blank');
        $operation->newSite($this->tmpDir, 'blank', false);
    }
}
