<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Scaffolding\NewSite;

class NewSiteTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpDir;
    protected $templatePath;

    public function setUp()
    {
        $this->templatePath = __DIR__.'/../fixtures/templates';
        $this->tmpDir = sys_get_temp_dir().'/spress-tests';
    }

    public function tearDown()
    {
        $fs = new FileSystem();
        $fs->remove($this->tmpDir);
    }

    public function testNewSiteBlank()
    {
        $operation = new NewSite($this->templatePath);
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

        $operation = new NewSite($this->templatePath);
        $operation->newSite($this->tmpDir, 'blank');

        $this->assertFileExists($this->tmpDir.'/config.yml');
        $this->assertFileExists($this->tmpDir.'/composer.json');
        $this->assertFileExists($this->tmpDir.'/src/content/index.html');
        $this->assertFileExists($this->tmpDir.'/src/content/posts');
        $this->assertFileExists($this->tmpDir.'/src/layouts');
    }

    public function testNewSiteBlankForce()
    {
        $operation = new NewSite($this->templatePath);
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
        $operation = new NewSite($this->templatePath);
        $operation->newSite($this->tmpDir, 'blank', false, true);

        $this->assertFileExists($this->tmpDir.'/config.yml');
        $this->assertFileExists($this->tmpDir.'/composer.json');
        $this->assertFileExists($this->tmpDir.'/src/content/index.html');
        $this->assertFileExists($this->tmpDir.'/src/content/posts');
        $this->assertFileExists($this->tmpDir.'/src/layouts');
        $this->assertFileExists($this->tmpDir.'/src/includes');
        $this->assertFileExists($this->tmpDir.'/src/plugins');
    }

    public function testNewSiteTemplateTest()
    {
        $operation = new NewSite($this->templatePath);
        $operation->newSite($this->tmpDir, 'template-test');

        $this->assertFileExists($this->tmpDir.'/config.yml');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNewSiteBlankNoForce()
    {
        $operation = new NewSite($this->templatePath);
        $operation->newSite($this->tmpDir, 'blank');
        $operation->newSite($this->tmpDir, 'blank', false);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNewSiteTemplateNotExists()
    {
        $operation = new NewSite($this->templatePath);
        $operation->newSite($this->tmpDir, 'template-not-exisits');
    }
}
