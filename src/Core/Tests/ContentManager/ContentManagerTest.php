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

use Symfony\Component\EventDispatcher\EventDispatcher;
use Yosymfony\Spress\Core\ContentManager\ContentManager;
use Yosymfony\Spress\Core\ContentManager\Generator\GeneratorManager;
use Yosymfony\Spress\Core\ContentManager\Generator\Pagination\PaginationGenerator;
use Yosymfony\Spress\Core\ContentManager\Collection\CollectionManagerBuilder;
use Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager;
use Yosymfony\Spress\Core\ContentManager\Converter\MichelfMarkdownConverter;
use Yosymfony\Spress\Core\ContentManager\Converter\MirrorConverter;
use Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkGenerator;
use Yosymfony\Spress\Core\ContentManager\Renderizer\TwigRenderizer;
use Yosymfony\Spress\Core\ContentManager\SiteAttribute\SiteAttribute;
use Yosymfony\Spress\Core\DataSource\DataSourceManagerBuilder;
use Yosymfony\Spress\Core\DataWriter\MemoryDataWriter;
use Yosymfony\Spress\Core\IO\NullIO;
use Yosymfony\Spress\Core\Plugin\PluginManager;
use Yosymfony\Spress\Core\Tester\PluginTester;

class ContentManagerTest extends \PHPUnit_Framework_TestCase
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

        $cm = $this->getContentManager($dw, [$testPlugin]);
        $cm->parseSite($attributes, $spressAttributes);

        $this->assertCount(14, $dw->getItems());

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
        $cm = $this->getContentManager($dw);
        $cm->parseSite($attributes, $spressAttributes, true);

        $this->assertCount(15, $dw->getItems());

        $this->assertTrue($dw->hasItem('books/2013/09/19/new-book/index.html'));

        $this->assertContains('<!DOCTYPE HTML>', $dw->getItem('books/2013/09/19/new-book/index.html')->getContent());
    }

    protected function getContentManager($dataWriter, array $plugins = [])
    {
        $dsm = $this->getDataSourceManager();
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
            ],
        ];

        $builder = new CollectionManagerBuilder();
        $cm = $builder->buildFromConfigArray($config);

        return $cm;
    }

    protected function getConverterManager()
    {
        $cm = new ConverterManager();
        $cm->addConverter(new MirrorConverter());
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

    protected function getDataSourceManager()
    {
        $config = [
            'data_source_name_1' => [
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

    private function getRenderizer()
    {
        $twigLoader = new \Twig_Loader_Array([]);
        $twig = new \Twig_Environment($twigLoader, ['autoescape' => false]);

        return new TwigRenderizer($twig, $twigLoader, ['twig', 'html.twig', 'twig.html', 'html']);
    }

    private function getPluginManager(EventDispatcher $dispatcher, array $plugins)
    {
        $pm = new PluginManager($dispatcher);

        foreach ($plugins as $index => $Plugin) {
            $pm->addPlugin($index, $Plugin);
        }

        return $pm;
    }
}
