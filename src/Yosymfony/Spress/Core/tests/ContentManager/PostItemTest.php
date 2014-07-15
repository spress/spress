<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\Tests\ContentManager;

use Symfony\Component\Finder\SplFileInfo;
use Yosymfony\Spress\Core\Application;
use Yosymfony\Spress\Core\ContentLocator\FileItem;
use Yosymfony\Spress\Core\ContentManager\PostItem;

class PostItemTest extends \PHPUnit_Framework_TestCase
{
    protected $postDir;
    protected $configuration;
    
    public function setUp()
    {
        $this->postDir = realpath(__DIR__ .'/../fixtures/project/_posts');
        
        $app = new Application();
        $this->configuration = $app['spress.config'];
    }
    
    public function testPostItemPrettyUrl()
    {
        $path = $this->postDir . '/2013-08-12-post-example-1.md';
        $fileInfo = new SplFileInfo($path, '', '2013-08-12-post-example-1.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        $this->configuration->getRepository()->set('permalink', 'pretty');
        $post = new PostItem($fileItem, $this->configuration);
        $post->setPreConverterContent('Test content');
        $post->setPostConverterContent($post->getPreConverterContent());
        $post->setPreLayoutContent('Test pre-layout');
        $post->setPostLayoutContent('Test post-layout');
        $post->setOutExtension('html');
        
        $this->assertGreaterThan(0, strlen($post->getId()));
        $this->assertEquals('New Post Example', $post->getTitle());
        $this->assertEquals('/category-1/category-2/2020/01/01/new-post-example/', $post->getUrl());
        $this->assertTrue($post->hasFrontmatter());
        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentManager\Frontmatter', $post->getFrontmatter());
        $this->assertEquals('default', $post->getFrontmatter()->getFrontmatter()->get('layout'));
        $this->assertEquals('Test content', $post->getPreConverterContent());
        $this->assertEquals('Test pre-layout', $post->getPreLayoutContent());
        $this->assertEquals('Test post-layout', $post->getPostLayoutContent());
        $this->assertEquals('New Post Example', $post->getTitle());
        $this->assertContains('category 1', $post->getCategories());
        $this->assertContains('tag2', $post->getTags());
        $this->assertFalse($post->isDraft());
        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentLocator\FileItem', $post->getFileItem());
        $this->assertEquals('2020-01-01', $post->getDate()->format('Y-m-d'));
        
        $payload = $post->getPayload();
        
        $this->assertEquals($post->getTitle(), $payload['title']);
        $this->assertEquals($post->getUrl(), $payload['url']);
        $this->assertEquals($post->getPostConverterContent(), $payload['content']);
        $this->assertEquals($post->getId(), $payload['id']);
        $this->assertEquals($post->getCategories(), $payload['categories']);
        $this->assertEquals($post->getTags(), $payload['tags']);
        $this->assertEquals($post->getDate()->format(\Datetime::ISO8601), $payload['date']);
        $this->assertEquals('/category-1/category-2/2020/01/01/new-post-example/index.html', $payload['path']);
    }
    
    public function testPostItemOrdinalUrl()
    {
        $path = $this->postDir . '/2013-08-12-post-example-1.md';
        $fileInfo = new SplFileInfo($path, '', '2013-08-12-post-example-1.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        $this->configuration->getRepository()->set('permalink', 'ordinal');
        $post = new PostItem($fileItem, $this->configuration);
        
        $this->assertEquals('/category-1/category-2/2020/1/new-post-example.html', $post->getUrl());
        
        $payload = $post->getPayload();
        
        $this->assertEquals($post->getUrl(), $payload['url']);
        $this->assertEquals('/category-1/category-2/2020/1/new-post-example.html', $payload['path']);
    }
    
    public function testPostItemDatelUrl()
    {
        $path = $this->postDir . '/2013-08-12-post-example-1.md';
        $fileInfo = new SplFileInfo($path, '', '2013-08-12-post-example-1.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        $this->configuration->getRepository()->set('permalink', 'date');
        $post = new PostItem($fileItem, $this->configuration);
        
        $this->assertEquals('/2020/01/01/new-post-example.html', $post->getUrl());
        
        $payload = $post->getPayload();
        
        $this->assertEquals($post->getUrl(), $payload['url']);
        $this->assertEquals('/2020/01/01/new-post-example.html', $payload['path']);
    }
    
    public function testPostItemCustomUrl()
    {
        $path = $this->postDir . '/2013-08-12-post-example-1.md';
        $fileInfo = new SplFileInfo($path, '', '2013-08-12-post-example-1.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        $this->configuration->getRepository()->set('permalink', '/blog/:year-:month-:day/:title/');
        $post = new PostItem($fileItem, $this->configuration);
        $post->setOutExtension('html');
        
        $this->assertEquals('/blog/2020-01-01/new-post-example/', $post->getUrl());
        
        $payload = $post->getPayload();
        
        $this->assertEquals($post->getUrl(), $payload['url']);
        $this->assertEquals('/blog/2020-01-01/new-post-example/index.html', $payload['path']);
    }
    
    public function testPostItemDirStructure()
    {
        $path = $this->postDir . '/books/2013-08-11-best-book.md';
        $fileInfo = new SplFileInfo($path, 'books', '2013-08-11-best-book.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        $this->configuration->getRepository()->set('permalink', 'pretty');
        $post = new PostItem($fileItem, $this->configuration);
        $post->setPreConverterContent('Test content');
        $post->setPostConverterContent($post->getPreConverterContent());
        $post->setOutExtension('html');
        
        $this->assertGreaterThan(0, strlen($post->getId()));
        $this->assertEquals('best book', $post->getTitle());
        $this->assertEquals('/books/2013/08/11/best-book/', $post->getUrl());
        $this->assertTrue($post->hasFrontmatter());
        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentManager\Frontmatter', $post->getFrontmatter());
        $this->assertEquals('default', $post->getFrontmatter()->getFrontmatter()->get('layout'));
        $this->assertEquals('Test content', $post->getPreConverterContent());
        $this->assertEquals('best book', $post->getTitle());
        $this->assertContains('books', $post->getCategories());
        $this->assertCount(0, $post->getTags());
        $this->assertFalse($post->isDraft());
        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentLocator\FileItem', $post->getFileItem());
        $this->assertEquals('2013-08-11', $post->getDate()->format('Y-m-d'));
        
        $payload = $post->getPayload();
        
        $this->assertEquals($post->getTitle(), $payload['title']);
        $this->assertEquals($post->getUrl(), $payload['url']);
        $this->assertEquals($post->getPostConverterContent(), $payload['content']);
        $this->assertEquals($post->getId(), $payload['id']);
        $this->assertEquals($post->getCategories(), $payload['categories']);
        $this->assertEquals($post->getTags(), $payload['tags']);
        $this->assertEquals($post->getDate()->format(\Datetime::ISO8601), $payload['date']);
        $this->assertEquals('/books/2013/08/11/best-book/index.html', $payload['path']);
    }
    
    public function testPostItemDraft()
    {
        $path = $this->postDir . '/books/2013-09-19-new-book.md';
        $fileInfo = new SplFileInfo($path, 'books', '2013-09-19-new-book.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        $this->configuration->getRepository()->set('permalink', 'pretty');
        $post = new PostItem($fileItem, $this->configuration);
        
        $this->assertGreaterThan(0, strlen($post->getId()));
        $this->assertGreaterThan(0, strlen($post->getPreConverterContent()));
        $this->assertTrue($post->isDraft());
    }
    
    public function testPostItemNonFrontmatter()
    {
        $path = $this->postDir . '/2013-08-12-post-example-2.mkd';
        $fileInfo = new SplFileInfo($path, '', '2013-08-12-post-example-2.mkd');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        $this->configuration->getRepository()->set('permalink', 'pretty');
        $post = new PostItem($fileItem, $this->configuration);
        $post->setPreConverterContent('Test content');
        $post->setPostConverterContent($post->getPreConverterContent());
        
        $this->assertGreaterThan(0, strlen($post->getId()));
        $this->assertEquals('post example 2', $post->getTitle());
        $this->assertEquals('/2013/08/12/post-example-2/', $post->getUrl());
        $this->assertFalse($post->hasFrontmatter());
        $this->assertInstanceOf('Yosymfony\Spress\Core\ContentManager\Frontmatter', $post->getFrontmatter());
        $this->assertEquals('Test content', $post->getPreConverterContent());
        $this->assertEquals('post example 2', $post->getTitle());
        $this->assertCount(0, $post->getCategories());
        $this->assertCount(0, $post->getTags());
        $this->assertInstanceOf('Yosymfony\\Spress\\Core\\ContentLocator\\FileItem', $post->getFileItem());
        $this->assertEquals('2013-08-12', $post->getDate()->format('Y-m-d'));
        
        $payload = $post->getPayload();
        
        $this->assertEquals($post->getTitle(), $payload['title']);
        $this->assertEquals($post->getUrl(), $payload['url']);
        $this->assertEquals($post->getPostConverterContent(), $payload['content']);
        $this->assertEquals($post->getId(), $payload['id']);
        $this->assertEquals($post->getCategories(), $payload['categories']);
        $this->assertEquals($post->getTags(), $payload['tags']);
        $this->assertEquals($post->getDate()->format(\Datetime::ISO8601), $payload['date']);
        $this->assertEquals('/2013/08/12/post-example-2/2013-08-12-post-example-2.mkd', $payload['path']);
    }
    
    public function testPostItemAbsoluteUrl()
    {
        $path = $this->postDir . '/2013-08-12-post-example-1.md';
        $fileInfo = new SplFileInfo($path, '', '2013-08-12-post-example-1.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        $this->configuration->getRepository()->set('permalink', 'pretty');
        $this->configuration->getRepository()->set('relative_permalinks', false);
        $post = new PostItem($fileItem, $this->configuration);
        
        $this->assertGreaterThan(0, strlen($post->getId()));
        $this->assertGreaterThan(0, strlen($post->getPreConverterContent()));
        $this->assertEquals('http://localhost:4000/category-1/category-2/2020/01/01/new-post-example/', $post->getUrl());   
    }
    
    public function testPostItemCustomKeyFrontmatter()
    {
        $path = $this->postDir . '/2013-08-12-post-example-1.md';
        $fileInfo = new SplFileInfo($path, '', '2013-08-12-post-example-1.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        $post = new PostItem($fileItem, $this->configuration);
        $payload = $post->getPayload();
        
        $this->assertEquals('My custom key', $payload['customKey']);
    }
}