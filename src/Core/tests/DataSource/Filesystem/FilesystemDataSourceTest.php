<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\DataSource\Filesystem;

use Yosymfony\Spress\Core\DataSource\Filesystem\FilesystemDataSource;

class FilesystemDataSourceTest extends \PHPUnit_Framework_TestCase
{
    protected $textExtensions;

    public function setUp()
    {
        $this->textExtensions = [ 'htm', 'html', 'html.twig', 'twig,html', 'js', 'less', 'markdown', 'md', 'mkd', 'mkdn', 'coffee', 'css', 'erb', 'haml', 'handlebars', 'hb', 'ms', 'mustache', 'php', 'rb', 'sass', 'scss', 'slim', 'txt', 'xhtml', 'xml' ];
    }

    public function testProcessItems()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/src',
            'text_extensions'   => $this->textExtensions,
        ]);

        $fsDataSource->load();

        $items = $fsDataSource->getItems();
        $layouts = $fsDataSource->getLayouts();
        $includes = $fsDataSource->getIncludes();

        $this->assertTrue(is_array($items));
        $this->assertTrue(is_array($layouts));
        $this->assertTrue(is_array($includes));

        $this->assertCount(12, $items);
        $this->assertCount(1, $layouts);
        $this->assertCount(1, $includes);

        $this->assertArrayHasKey('about/index.html', $items);
        $this->assertArrayHasKey('about/me/index.html', $items);
        $this->assertArrayHasKey('index.html', $items);
        $this->assertArrayHasKey('LICENSE', $items);
        $this->assertArrayHasKey('pages/index.html', $items);
        $this->assertArrayHasKey('projects/index.md', $items);
        $this->assertArrayHasKey('robots.txt', $items);
        $this->assertArrayHasKey('sitemap.xml', $items);
        $this->assertArrayHasKey('posts/2013-08-12-post-example-1.md', $items);
        $this->assertArrayHasKey('posts/2013-08-12-post-example-2.mkd', $items);
        $this->assertArrayHasKey('posts/books/2013-08-11-best-book.md', $items);
        $this->assertArrayHasKey('posts/books/2013-09-19-new-book.md', $items);

        $itemAttributes = $items['about/index.html']->getAttributes();
        $this->assertCount(4, $itemAttributes);
        $this->assertEquals('default', $itemAttributes['layout']);

        $itemAttributes = $items['posts/2013-08-12-post-example-1.md']->getAttributes();
        $this->assertCount(10, $itemAttributes);
        $this->assertArrayNotHasKey('meta_filename', $itemAttributes);
        $this->assertStringStartsWith('Post example 1', $items['posts/2013-08-12-post-example-1.md']->getContent());

        $itemAttributes = $items['posts/2013-08-12-post-example-2.mkd']->getAttributes();
        $this->assertArrayHasKey('title', $itemAttributes);
        $this->assertArrayHasKey('title_path', $itemAttributes);
        $this->assertArrayHasKey('date', $itemAttributes);
        $this->assertEquals('post example 2', $itemAttributes['title']);
        $this->assertEquals('2013-08-12', $itemAttributes['date']);

        $itemAttributes = $items['sitemap.xml']->getAttributes();
        $this->assertEquals('sitemap', $itemAttributes['name']);

        $this->assertArrayHasKey('default.html', $layouts);

        $this->assertArrayHasKey('test.html', $includes);

        $this->assertFalse($items['posts/2013-08-12-post-example-1.md']->isBinary());

        $this->assertEquals('layout', $layouts['default.html']->getType());

        $include = $includes['test.html'];

        $this->assertEquals('include', $include->getType());

        $this->assertTrue($items['LICENSE']->isBinary());
        $this->assertTrue(strlen($items['LICENSE']->getPath('source')) > 0);
        $this->assertTrue(strlen($items['LICENSE']->getPath('relative')) > 0);
    }

    public function testIncludeFile()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/src',
            'include'           => [__dir__.'/../../fixtures/extra_pages/extra-page1.html'],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();
        $this->assertCount(13, $fsDataSource->getItems());
    }

    public function testIncludeFolder()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/src',
            'include'           => [__dir__.'/../../fixtures/extra_pages'],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(14, $fsDataSource->getItems());
    }

    public function testExcludeFile()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/src',
            'exclude'           => ['robots.txt'],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(11, $fsDataSource->getItems());
    }

    public function testExcludeFolder()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/src',
            'exclude'           => ['about'],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(6, $fsDataSource->getItems());
    }

    public function testConfigOnlySourceRootParam()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/src',
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(8, $fsDataSource->getItems());
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\MissingAttributeException
     */
    public function testConfigNoParams()
    {
        $fsDataSource = new FilesystemDataSource([]);
        $fsDataSource->load();
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\MissingAttributeException
     */
    public function testNoParamTextExtensions()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'   => __dir__.'/../../fixtures/project/src',
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\AttributeValueException
     */
    public function testBadParamSourceRoot()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => [],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\AttributeValueException
     */
    public function testBadParamInclude()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/src',
            'include'           => './',
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\AttributeValueException
     */
    public function testBadParamExclude()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/src',
            'exclude'           => './',
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }
}
