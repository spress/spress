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

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Yosymfony\Spress\Core\ContentManager\ContentManager;
use Yosymfony\Spress\Core\ContentManager\Generator\GeneratorManager;
use Yosymfony\Spress\Core\ContentManager\Generator\Pagination\PaginationGenerator;
use Yosymfony\Spress\Core\ContentManager\Collection\CollectionManagerBuilder;
use Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager;
use Yosymfony\Spress\Core\ContentManager\Converter\MichelfMarkdownConverter;
use Yosymfony\Spress\Core\ContentManager\Converter\MapConverter;
use Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkGenerator;
use Yosymfony\Spress\Core\ContentManager\Renderizer\TwigRenderizer;
use Yosymfony\Spress\Core\ContentManager\SiteAttribute\SiteAttribute;
use Yosymfony\Spress\Core\DataSource\DataSourceManagerBuilder;
use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\DataWriter\MemoryDataWriter;
use Yosymfony\Spress\Core\IO\NullIO;
use Yosymfony\Spress\Core\Plugin\PluginManager;
use Yosymfony\Spress\Core\Tester\PluginTester;

class ContentManagerTest extends TestCase
{
    public function setUp()
    {
        \Twig_Autoloader::register();
    }

    public function testParseSite()
    {
        $attributes = [
            'site_name' => 'My tests site',
        ];

        $spressAttributes = [
            'version' => '2.0.0',
            'version_id' => '20000',
            'major_version' => '2',
            'minor_version' => '0',
            'release_version' => '0',
            'extra_version' => 'dev',
        ];

        $dw = new MemoryDataWriter();

        $testPlugin = new PluginTester('acme');
        $testPlugin->setListenerToBeforeRenderPageEvent(function ($event) {
            if ($event->getId() === 'about/index.html') {
                $this->assertStringEndsNotWith('</html>', $event->getContent());
            }
        });
        $testPlugin->setListenerToAfterRenderPageEvent(function ($event) {
            if ($event->getId() === 'about/index.html') {
                $this->assertStringEndsWith('</html>', $event->getContent());
            }
        });

        $cm = $this->getContentManager($this->getFilesystemDataSourceManager(), $dw, [$testPlugin]);
        $cm->parseSite($attributes, $spressAttributes);

        $this->assertCount(17, $dw->getItems());

        $this->assertTrue($dw->hasItem('about/index.html'));
        $this->assertTrue($dw->hasItem('about/me/index.html'));
        $this->assertTrue($dw->hasItem('index.html'));
        $this->assertTrue($dw->hasItem('LICENSE'));
        $this->assertTrue($dw->hasItem('category-1/category-2/2020/01/01/new-post-example/index.html'));
        $this->assertTrue($dw->hasItem('2013/08/12/post-example-2/index.html'));
        $this->assertTrue($dw->hasItem('books/2013/08/11/best-book/index.html'));
        $this->assertTrue($dw->hasItem('projects/index.html'));
        $this->assertTrue($dw->hasItem('robots.txt'));
        $this->assertTrue($dw->hasItem('sitemap.xml'));
        $this->assertTrue($dw->hasItem('pages/index.html'));
        $this->assertTrue($dw->hasItem('pages/page2/index.html'));
        $this->assertTrue($dw->hasItem('pages/page3/index.html'));
        $this->assertTrue($dw->hasItem('pages/page4/index.html'));
        $this->assertTrue($dw->hasItem('2016/02/02/spress-2-1-1-released/index.html'));

        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('about/index.html')->getContent());
        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('pages/index.html')->getContent());
        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('pages/page2/index.html')->getContent());

        $attributes = $dw->getItem('books/2013/08/11/best-book/index.html')->getAttributes();
        $this->assertArrayHasKey('author', $attributes);
        $this->assertArrayHasKey('categories', $attributes);
        $this->assertContains('books', $attributes['categories']);
        $this->assertEquals('Yo! Symfony', $attributes['author']);
    }

    public function testParseDraft()
    {
        $dw = new MemoryDataWriter();
        $cm = $this->getContentManager($this->getFilesystemDataSourceManager(), $dw);
        $cm->parseSite([], [], true);

        $this->assertCount(19, $dw->getItems());

        $this->assertTrue($dw->hasItem('books/2013/09/19/new-book/index.html'));

        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('books/2013/09/19/new-book/index.html')->getContent());
    }

    public function testRelationships()
    {
        $dw = new MemoryDataWriter();

        $testPlugin = new PluginTester('acme');
        $testPlugin->setListenerToBeforeRenderPageEvent(function ($event) {
            if ($event->getId() === 'about/index.html') {
                $this->assertStringEndsNotWith('</html>', $event->getContent());
            }
        });
        $testPlugin->setListenerToAfterRenderPageEvent(function ($event) {
            if ($event->getId() === 'about/index.html') {
                $this->assertStringEndsWith('</html>', $event->getContent());
            }
        });

        $cm = $this->getContentManager($this->getFilesystemDataSourceManager(), $dw, [$testPlugin]);
        $cm->parseSite([], [], true);

        $item = $dw->getItem('category-1/category-2/2020/01/01/new-post-example/index.html');
        $relationshipCollection = $item->getRelationshipCollection();

        $this->assertEquals(1, $relationshipCollection->count());
        $this->assertEquals(1, count($relationshipCollection->get('next')));

        $item = current($relationshipCollection->get('next'));
        $relationshipCollection = $item->getRelationshipCollection();

        $this->assertEquals('posts/2016-02-02-spress-2.1.1-released.md', $item->getId());
        $this->assertEquals(2, $relationshipCollection->count());
        $this->assertEquals(1, count($relationshipCollection->get('prior')));
        $this->assertEquals(1, count($relationshipCollection->get('next')));

        $nextItem = current($relationshipCollection->get('next'));
        $priorItem = current($relationshipCollection->get('prior'));

        $this->assertEquals('posts/books/2013-09-19-new-book.md', $nextItem->getId());
        $this->assertEquals('posts/2013-08-12-post-example-1.md', $priorItem->getId());

        $item = current($relationshipCollection->get('next'));
        $relationshipCollection = $item->getRelationshipCollection();

        $this->assertEquals('posts/books/2013-09-19-new-book.md', $item->getId());
        $this->assertEquals(2, $relationshipCollection->count());
        $this->assertEquals(1, count($relationshipCollection->get('prior')));
        $this->assertEquals(1, count($relationshipCollection->get('next')));

        $nextItem = current($relationshipCollection->get('next'));
        $priorItem = current($relationshipCollection->get('prior'));

        $this->assertEquals('posts/2013-08-12-post-example-2.mkd', $nextItem->getId());
        $this->assertEquals('posts/2016-02-02-spress-2.1.1-released.md', $priorItem->getId());

        $item = current($relationshipCollection->get('next'));
        $relationshipCollection = $item->getRelationshipCollection();

        $this->assertEquals('posts/2013-08-12-post-example-2.mkd', $item->getId());
        $this->assertEquals(2, $relationshipCollection->count());
        $this->assertEquals(1, count($relationshipCollection->get('prior')));
        $this->assertEquals(1, count($relationshipCollection->get('next')));

        $nextItem = current($relationshipCollection->get('next'));
        $priorItem = current($relationshipCollection->get('prior'));

        $this->assertEquals('posts/books/2013-08-11-best-book.md', $nextItem->getId());
        $this->assertEquals('posts/books/2013-09-19-new-book.md', $priorItem->getId());

        $item = current($relationshipCollection->get('next'));
        $relationshipCollection = $item->getRelationshipCollection();

        $this->assertEquals('posts/books/2013-08-11-best-book.md', $item->getId());
        $this->assertEquals(1, $relationshipCollection->count());
        $this->assertEquals(1, count($relationshipCollection->get('prior')));
    }

    public function testMultipleExtension()
    {
        $dw = new MemoryDataWriter();
        $dsm = $this->getMemoryDataSourceManager();
        $cm = $this->getContentManager($dsm, $dw);

        $item1 = new Item('', 'about.html.twig');
        $item1->setPath('about.html.twig', Item::SNAPSHOT_PATH_RELATIVE);

        $item2 = new Item('', 'docs/migrating-1.x-to-2.x.md');
        $item2->setPath('docs/migrating-1.x-to-2.x.md', Item::SNAPSHOT_PATH_RELATIVE);

        $memoryDataSource = $dsm->getDataSource('memory');
        $memoryDataSource->addItem($item1);
        $memoryDataSource->addItem($item2);

        $cm->parseSite([], [], true);

        $this->assertTrue($dw->hasItem('about/index.html'));
        $this->assertTrue($dw->hasItem('docs/migrating-1.x-to-2.x/index.html'));
    }

    protected function getContentManager($dataSourceManager, $dataWriter, array $plugins = [])
    {
        $dsm = $dataSourceManager;
        $gm = $this->getGeneratorManager();
        $cm = $this->getConverterManager();
        $com = $this->getCollectionManager();
        $pg = new PermalinkGenerator('pretty');
        $renderizer = $this->getRenderizer();
        $siteAttribute = new SiteAttribute();
        $dispatcher = new EventDispatcher();
        $pm = $this->getPluginManager($dispatcher, $plugins);
        $io = new NullIO();

        return new ContentManager($dsm, $dataWriter, $gm, $cm, $com, $pg, $renderizer, $siteAttribute, $pm, $dispatcher, $io);
    }

    protected function getCollectionManager()
    {
        $config = [
            'posts' => [
                'output' => true,
                'author' => 'Yo! Symfony',
                'sort_by' => 'date',
                'sort_type' => 'descending',
            ],
        ];

        $builder = new CollectionManagerBuilder();
        $cm = $builder->buildFromConfigArray($config);

        return $cm;
    }

    protected function getConverterManager()
    {
        $cm = new ConverterManager(['html', 'html.twig', 'twig.html', 'md']);
        $cm->addConverter(new MapConverter(['html.twig' => 'html']));
        $cm->addConverter(new MichelfMarkdownConverter(['markdown', 'mkd', 'mkdn', 'md']));

        return $cm;
    }

    protected function getGeneratorManager()
    {
        $generator = new PaginationGenerator();

        $gm = new GeneratorManager();
        $gm->addGenerator('pagination', $generator);

        return $gm;
    }

    protected function getFilesystemDataSourceManager()
    {
        $config = [
            'filesystem' => [
                'class' => 'Yosymfony\Spress\Core\DataSource\Filesystem\FilesystemDataSource',
                'arguments' => [
                    'source_root' => __dir__.'/../fixtures/project/src',
                    'text_extensions' => ['htm', 'html', 'html.twig', 'twig.html', 'js', 'less', 'markdown', 'md', 'mkd', 'mkdn', 'coffee', 'css', 'txt', 'xhtml', 'xml'],
                ],
            ],
        ];

        $builder = new DataSourceManagerBuilder();

        return $builder->buildFromConfigArray($config);
    }

    protected function getMemoryDataSourceManager()
    {
        $config = [
            'memory' => [
                'class' => 'Yosymfony\Spress\Core\DataSource\Memory\MemoryDataSource',
            ],
        ];

        $builder = new DataSourceManagerBuilder();

        return $builder->buildFromConfigArray($config);
    }

    protected function getRenderizer()
    {
        $twigLoader = new \Twig_Loader_Array([]);
        $twig = new \Twig_Environment($twigLoader, ['autoescape' => false]);

        return new TwigRenderizer($twig, $twigLoader, ['twig', 'html.twig', 'twig.html', 'html']);
    }

    protected function getPluginManager(EventDispatcher $dispatcher, array $plugins)
    {
        $pm = new PluginManager($dispatcher);
        $pluginCollection = $pm->getPluginCollection();

        foreach ($plugins as $index => $Plugin) {
            $pluginCollection->add($index, $Plugin);
        }

        return $pm;
    }
}
