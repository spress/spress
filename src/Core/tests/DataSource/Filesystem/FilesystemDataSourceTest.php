<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\DataSource\Filesystem;

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
            'source_root'       => __dir__.'/../../fixtures/project/',
            'layouts_root'      => __dir__.'/../../fixtures/project/_layouts/',
            'includes_root'     => __dir__.'/../../fixtures/project/_includes/',
            'posts_root'        => __dir__.'/../../fixtures/project/_posts/',
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
        $this->assertArrayHasKey('2013-08-12-post-example-1.md', $items);
        $this->assertArrayHasKey('2013-08-12-post-example-2.mkd', $items);
        $this->assertArrayHasKey('books/2013-08-11-best-book.md', $items);
        $this->assertArrayHasKey('books/2013-09-19-new-book.md', $items);

        $itemAttributes =  $items['about/index.html']->getAttributes();
        $this->assertCount(4, $itemAttributes);
        $this->assertEquals('default', $itemAttributes['layout']);

        $itemAttributes =  $items['2013-08-12-post-example-1.md']->getAttributes();
        $this->assertCount(9, $itemAttributes);
        $this->assertArrayNotHasKey('meta_filename', $itemAttributes);
        $this->assertStringStartsWith('Post example 1', $items['2013-08-12-post-example-1.md']->getContent());

        $itemAttributes =  $items['sitemap.xml']->getAttributes();
        $this->assertEquals('sitemap.xml.meta', $itemAttributes['meta_filename']);
        $this->assertEquals('sitemap', $itemAttributes['name']);

        $this->assertArrayHasKey('default.html', $layouts);

        $this->assertArrayHasKey('test.html', $includes);

        $this->assertFalse($items['2013-08-12-post-example-1.md']->isBinary());

        $this->assertEquals('layout', $layouts['default.html']->getType());

        $include = $includes['test.html'];

        $this->assertEquals('include', $include->getType());
    }

    public function testIncludeFile()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
            'include'           => ['.htaccess'],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(9, $fsDataSource->getItems());
    }

    public function testIncludeFolder()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
            'include'           => ['../extra_pages'],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(10, $fsDataSource->getItems());
    }

    public function testExcludeFile()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
            'exclude'           => ['robots.txt'],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(7, $fsDataSource->getItems());
    }

    public function testExcludeFolder()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
            'exclude'           => ['about'],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(6, $fsDataSource->getItems());
    }

    public function testConfigOnlySourceRootParam()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();

        $this->assertCount(8, $fsDataSource->getItems());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testConfigNoParams()
    {
        $fsDataSource = new FilesystemDataSource([]);
        $fsDataSource->load();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoParamTextExtensions()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException RuntimeException
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
     * @expectedException RuntimeException
     */
    public function testBadParamPostsRoot()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
            'posts_root'        => [],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testBadParamLayoutsRoot()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
            'layouts_root'      => [],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testBadParamIncludesRoot()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
            'includes_root'     => [],
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testBadParamInclude()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
            'include'           => './',
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testBadParamExclude()
    {
        $fsDataSource = new FilesystemDataSource([
            'source_root'       => __dir__.'/../../fixtures/project/',
            'exclude'           => './',
            'text_extensions'   => $this->textExtensions,
        ]);
        $fsDataSource->load();
    }
}
