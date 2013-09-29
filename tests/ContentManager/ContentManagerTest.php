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

use Yosymfony\Spress\Application;

class ContentManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $cms;
    protected $config;
    protected $destination;
    
    public function setUp()
    {
        $this->app = new Application(); 
        $this->app['spress.config']->loadLocal('./tests/fixtures/project');
        $this->cms = $this->app['spress.cms'];
        $this->destination = $this->app['spress.content_locator']->getDestinationDir();
    }
    
    public function testProcessSite()
    {
        $this->cms->processSite();
        
        $this->assertFileNotExists($this->destination . '/config.yml');
        $this->assertFileExists($this->destination . '/index.html');
        $this->assertFileExists($this->destination . '/sitemap.xml');
        $this->assertFileExists($this->destination . '/robots.txt');
        $this->assertFileExists($this->destination . '/about/index.html');
        $this->assertFileExists($this->destination . '/about/me/index.html');
        $this->assertFileExists($this->destination . '/projects/index.html');
        $this->assertFileExists($this->destination . '/books/2013/08/11/best-book/index.html');
        $this->assertFileNotExists($this->destination . '/books/2013/09/19/new-book/index.html');
        $this->assertFileExists($this->destination . '/category-1/category-2/2020/01/01/new-post-example/index.html');
        $this->assertFileNotExists($this->destination . '/2013/08/12/post-example-2/2013-08-12-post-example-2.mkd');
    }
    
    public function testProcessSiteDraft()
    {
        $this->app['spress.config']->getRepository()->set('drafts', true);
        
        $this->cms->processSite();
        
        $this->assertFileExists($this->destination . '/books/2013/08/11/best-book/index.html');
        $this->assertFileExists($this->destination . '/books/2013/09/19/new-book/index.html');
        $this->assertFileExists($this->destination . '/category-1/category-2/2020/01/01/new-post-example/index.html');
        $this->assertFileNotExists($this->destination . '/2013/08/12/post-example-2/2013-08-12-post-example-2.mkd');
    }
    
    /**
     * @expectedException Yosymfony\Spress\Exception\FrontmatterValueException
     */
    public function testProcessSiteWithNotExistsLayout()
    {
        $this->app['spress.config']->getRepository()->set('include', array('../extra_pages/extra-page2.html'));
        
        $this->cms->processSite();    
    }
    
    public function testProcessSiteWithPaginator()
    {
        $this->app['spress.config']->getRepository()->set('paginate', 1);
        
        $this->cms->processSite();
        
        $this->assertFileExists($this->destination . '/page2/index.html');
    }
}