<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\Tests\ContentLocator;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Core\Application;
use Yosymfony\Spress\Core\ContenLocator\ContenLocator;

class ContentLocatorTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $projectDir;
    protected $config;
    protected $contentLocator;
    
    public function setUp()
    {
        $this->app = new Application();
        $this->projectDir = (__DIR__ . '/../fixtures/project');
        $this->config = $this->app['spress.config'];
        $this->config->loadLocal($this->projectDir);
        $this->contentLocator = $this->app['spress.content_locator'];
        $this->contentLocator->setConvertersExtension($this->config->getRepository()->get('markdown_ext'));
    }
    
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove($this->projectDir . '/_site');
    }
    
    public function testGetPosts()
    {
        $posts = $this->contentLocator->getPosts();
        
        $this->assertCount(4, $posts);
        $this->assertContainsOnlyInstancesOf('Yosymfony\Spress\Core\ContentLocator\FileItem', $posts);
    }
    
    public function testGetPostsWithEmptyMarkdownExtesion()
    {
        $this->contentLocator->setConvertersExtension([]);
        $posts = $this->contentLocator->getPosts();
        
        $this->assertCount(0, $posts);
    }
    
    public function testGetPages()
    {
        $pages = $this->contentLocator->getPages();

        $this->assertCount(6, $pages);
        $this->assertContainsOnlyInstancesOf('Yosymfony\Spress\Core\ContentLocator\FileItem', $pages);
    }
    
    public function testGetPagesWithEmptyProcessableExtesion()
    {
        $this->config->getRepository()->set('processable_ext', array());
        $this->contentLocator->setConvertersExtension([]);
        $items = $this->contentLocator->getPages();
        
        $this->assertCount(0, $items);
    }
    
    public function testGetPagesWithIncludeFile()
    {
        $this->config->getRepository()->set('include', array('../extra_pages/extra-page1.html'));
        $pages = $this->contentLocator->getPages();
        
        $filenames = array();
        foreach($pages as $page)
        {
            $filenames[] = $page->getFileName();
        }

        $this->assertContains('extra-page1.html', $filenames);
    }
    
    public function testGetPagesWithIncludeDir()
    {
        $this->config->getRepository()->set('include', array('../extra_pages'));
        $pages = $this->contentLocator->getPages();
        
        $filenames = array();
        foreach($pages as $page)
        {
            $filenames[] = $page->getFileName();
        }
        
        $this->assertContains('extra-page1.html', $filenames);
        $this->assertContains('extra-page2.html', $filenames);
    }
    
    public function testGetPagesWithExcludeFile()
    {
        $this->config->getRepository()->set('exclude', array('about/me/index.html'));
        $pages = $this->contentLocator->getPages();
        
        $filenames = array();
        foreach($pages as $page)
        {
            $filenames[] = $page->getRelativePath() . '/' . $page->getFileName();
        }

        $this->assertNotContains('about/me/index.html', $filenames);
    }
    
    public function testGetPagesWithExcludeDir()
    {
        $this->config->getRepository()->set('exclude', array('about'));
        $pages = $this->contentLocator->getPages();
        
        $filenames = array();
        foreach($pages as $page)
        {
            $filenames[] = $page->getRelativePath();
        }

        $this->assertNotContains('about', $filenames);
    }
    
    public function testGetPagesIncludeExclude()
    {
        $this->config->getRepository()->set('include', array('../extra_pages'));
        $this->config->getRepository()->set('exclude', array('extra-page2.html'));
        $pages = $this->contentLocator->getPages();
        
        $filenames = array();
        foreach($pages as $page)
        {
            $filenames[] = $page->getFilename();
        }
        
        $this->assertContains('extra-page1.html', $filenames);
        $this->assertNotContains('extra-page2.html', $filenames);
    }
    
    public function testGetItem()
    {
        $fileItem = $this->contentLocator->getItem('index.html');
        
        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentLocator\FileItem', $fileItem);
        $this->assertEquals($fileItem->getRelativePath(), '');
        $this->assertEquals($fileItem->getRelativePathFilename(), 'index.html');
    }
    
    public function testGetItemNotRelativePath()
    {
        $fileItem = $this->contentLocator->getItem('../extra_pages/extra-page1.html');
        
        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentLocator\FileItem', $fileItem);
        $this->assertEquals($fileItem->getRelativePath(), '');
        $this->assertEquals($fileItem->getRelativePathFilename(), 'extra-page1.html');
    }
    
    public function testGetItemNotExists()
    {
        $fileItem = $this->contentLocator->getItem('no-exists-dir/index.html');
        
        $this->assertNull($fileItem);
    }
    
    public function testGetLayouts()
    {
        $layouts = $this->contentLocator->getLayouts();
        
        $this->assertCount(1, $layouts);
        $this->assertContainsOnlyInstancesOf('Yosymfony\Spress\Core\ContentLocator\FileItem', $layouts);
    }
    
    public function testGetSourceDir()
    { 
        $this->assertTrue(strlen($this->contentLocator->getSourceDir()) > 0);
    }
    
    public function testGetPostsDir()
    { 
        $this->assertTrue(strlen($this->contentLocator->getPostsDir()) > 0);
    }
    
    public function testGetLayoutsDir()
    { 
        $this->assertTrue(strlen($this->contentLocator->getLayoutsDir()) > 0);
    }
    
    public function testGetDestinationDir()
    { 
        $this->assertTrue(strlen($this->contentLocator->getDestinationDir()) > 0);
    }
    
    public function testGetIncludesDir()
    { 
        $this->assertTrue(strlen($this->contentLocator->getIncludesDir()) > 0);
    }
    
    public function testCleanupDestination()
    {
        $destination = $this->contentLocator->getDestinationDir();
        $path = sprintf('%s/%s.html', $destination, microtime());
        $dir = sprintf('%s/test-dir', $destination);
        
        $fs = new Filesystem();
        $fs->dumpFile($path, '<h1>hello world</h1>');
        $fs->mkdir($dir);
        
        $this->contentLocator->cleanupDestination();
        
        $finder = new Finder();
        $finder->in($destination);
        
        $this->assertCount(0, $finder);
    }
    
    public function testCopyRestToDestination()
    {
        $this->contentLocator->cleanupDestination();
        $othersFiles = $this->contentLocator->copyRestToDestination();
        
        $filenames = array();
        foreach($othersFiles as $item)
        {
            $filenames[] = pathinfo($item, PATHINFO_BASENAME);
        }
        
        $this->assertCount(3, $filenames);
        $this->assertContains('.htaccess', $filenames);
        $this->assertNotContains('config.yml', $filenames);
    }
    
    public function testSaveItem()
    {
        $destination = $this->contentLocator->getDestinationDir();
        $this->contentLocator->cleanupDestination();
        $pages = $this->contentLocator->getPages();
        
        foreach($pages as $page)
        {
            $page->setDestinationPaths(array('test/save/' . $page->getFilename(), 'test/save2/' . $page->getFilename()));
            $this->contentLocator->saveItem($page);
        }
        
        $finder = new Finder();
        $finder->in($destination)->files();
        
        $filenames = array();
        foreach($finder as $file)
        {
            $filenames[] = $file->getRelativePathname();
        }
        
        $this->assertCount(6, $filenames);
        $this->assertContains('test/save/index.html', $filenames);
        $this->assertContains('test/save2/index.html', $filenames);
    }
    
    /**
     * @expectedException \LengthException
     */
    public function testSaveItemFailWithoutDestinations()
    {
        $this->contentLocator->cleanupDestination();
        $pages = $this->contentLocator->getPages();
        
        foreach($pages as $page)
        {
            $this->contentLocator->saveItem($page);
        }
    }
}