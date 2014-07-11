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
use Yosymfony\Spress\Operation\NewOperation;

class NewOperationTest extends \PHPUnit_Framework_TestCase
{
    protected $base;
    protected $templatePath;
    
    public function setUp()
    {
        $this->templatePath = './tests/fixtures/templates';
        $this->base = './tests/out';
        
        $fs = new FileSystem();
        $fs->remove($this->base);
    }
    
    public function testNewSiteBlank()
    {
        $operation = new NewOperation($this->templatePath);
        $operation->newSite($this->base, 'blank');
        
        $this->assertFileExists($this->base . '/config.yml');
        $this->assertFileExists($this->base . '/index.html');
        $this->assertFileExists($this->base . '/_posts');
        $this->assertFileExists($this->base . '/_layouts');
    }
    
    public function testNewSiteExistsEmptyDir()
    {
        $fs = new FileSystem();
        $fs->mkdir($this->base);
        
        $this->assertFileExists($this->base);
        
        $operation = new NewOperation($this->templatePath);
        $operation->newSite($this->base, 'blank');
        
        $this->assertFileExists($this->base . '/config.yml');
        $this->assertFileExists($this->base . '/composer.json');
        $this->assertFileExists($this->base . '/index.html');
        $this->assertFileExists($this->base . '/_posts');
        $this->assertFileExists($this->base . '/_layouts');
    }
    
    public function testNewSiteBlankForce()
    {
        $operation = new NewOperation($this->templatePath);
        $operation->newSite($this->base, 'blank');
        $operation->newSite($this->base, 'blank', true);
        
        $this->assertFileExists($this->base . '/config.yml');
        $this->assertFileExists($this->base . '/composer.json');
        $this->assertFileExists($this->base . '/index.html');
        $this->assertFileExists($this->base . '/_posts');
        $this->assertFileExists($this->base . '/_layouts');
    }
    
    public function testNewSiteBlankCompleteScaffold()
    {
        $operation = new NewOperation($this->templatePath);
        $operation->newSite($this->base, 'blank', false, true);
        
        $this->assertFileExists($this->base . '/config.yml');
        $this->assertFileExists($this->base . '/composer.json');
        $this->assertFileExists($this->base . '/index.html');
        $this->assertFileExists($this->base . '/_posts');
        $this->assertFileExists($this->base . '/_layouts');
        $this->assertFileExists($this->base . '/_includes');
        $this->assertFileExists($this->base . '/_plugins');
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testNewSiteBlankNoForce()
    {
        $operation = new NewOperation($this->templatePath);
        $operation->newSite($this->base, 'blank');
        $operation->newSite($this->base, 'blank', false);
    }
    
    public function testNewSiteTemplateTest()
    {
        $operation = new NewOperation($this->templatePath);
        $operation->newSite($this->base, 'template-test');
        
        $this->assertFileExists($this->base . '/config.yml');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNewSiteTemplateNotExists()
    {
        $operation = new NewOperation($this->templatePath);
        $operation->newSite($this->base, 'template-not-exisits');
    }
}