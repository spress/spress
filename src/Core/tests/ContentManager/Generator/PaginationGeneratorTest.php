<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\ContentManager\Generator;

use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\ContentManager\Generator\PaginationGenerator;
use Yosymfony\Spress\Core\Support\SupportFacade;

class PaginationGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $pagination;

    public function setUp()
    {
        $this->pagination = new PaginationGenerator(new SupportFacade());
    }

    public function testPaginateWithDefaulPermalink()
    {
        $content = 'Paginator content';
        $attributes = [
            'site' => [
                'posts' => [
                    '_posts/2015-05-26-hi' => [
                        'content' => 'My content 1',
                    ],
                    '_posts/2015-05-26-welcome' => [
                        'content' => 'My content 2',
                    ],
                ],
            ],
        ];

        $templateItem = new Item($content, 'blog/index.html', ['max_page' => 1]);
        $templateItem->setPath('blog/index.html', Item::SNAPSHOT_PATH_RELATIVE);

        $pageItems = $this->pagination->generateItems($templateItem, $attributes);

        $this->assertTrue(is_array($pageItems));
        $this->assertCount(2, $pageItems);
        $this->assertContainsOnly('\Yosymfony\Spress\Core\DataSource\ItemInterface', $pageItems);

        $page1 = $pageItems[0];

        $this->assertEquals('blog/index.html', $page1->getPath(Item::SNAPSHOT_PATH_RELATIVE));
        $this->assertEquals('/blog', $page1->getPath(Item::SNAPSHOT_PATH_PERMALINK));
        $this->assertEquals('Paginator content', $page1->getContent());

        $attrPage1 = $page1->getAttributes();

        $this->assertArrayHasKey('pagination', $attrPage1);
        $this->assertArrayHasKey('items', $attrPage1['pagination']);
        $this->assertArrayHasKey('per_page', $attrPage1['pagination']);
        $this->assertArrayHasKey('total_items', $attrPage1['pagination']);
        $this->assertArrayHasKey('total_pages', $attrPage1['pagination']);
        $this->assertArrayHasKey('page', $attrPage1['pagination']);
        $this->assertArrayHasKey('previous_page', $attrPage1['pagination']);
        $this->assertArrayHasKey('previous_page_path', $attrPage1['pagination']);
        $this->assertArrayHasKey('previous_page_url', $attrPage1['pagination']);
        $this->assertArrayHasKey('next_page', $attrPage1['pagination']);
        $this->assertArrayHasKey('next_page_path', $attrPage1['pagination']);
        $this->assertArrayHasKey('next_page_url', $attrPage1['pagination']);

        $this->assertCount(1, $attrPage1['pagination']['items']);
        $this->assertEquals(1, $attrPage1['pagination']['per_page']);
        $this->assertEquals(2, $attrPage1['pagination']['total_items']);
        $this->assertEquals(2, $attrPage1['pagination']['total_pages']);
        $this->assertEquals(1, $attrPage1['pagination']['page']);
        $this->assertEquals(2, $attrPage1['pagination']['next_page']);
        $this->assertEquals('blog/page2/index.html', $attrPage1['pagination']['next_page_path']);
        $this->assertEquals('/blog/page2', $attrPage1['pagination']['next_page_url']);

        $this->assertNull($attrPage1['pagination']['previous_page']);
        $this->assertNull($attrPage1['pagination']['previous_page_path']);
        $this->assertNull($attrPage1['pagination']['previous_page_url']);

        $page2 = $pageItems[1];

        $this->assertEquals('blog/page2/index.html', $page2->getPath(Item::SNAPSHOT_PATH_RELATIVE));
        $this->assertEquals('/blog/page2', $page2->getPath(Item::SNAPSHOT_PATH_PERMALINK));
        $this->assertEquals('Paginator content', $page1->getContent());

        $attrPage2 = $page2->getAttributes();

        $this->assertArrayHasKey('pagination', $attrPage2);
        $this->assertArrayHasKey('items', $attrPage2['pagination']);
        $this->assertArrayHasKey('per_page', $attrPage2['pagination']);
        $this->assertArrayHasKey('total_items', $attrPage2['pagination']);
        $this->assertArrayHasKey('total_pages', $attrPage2['pagination']);
        $this->assertArrayHasKey('page', $attrPage2['pagination']);
        $this->assertArrayHasKey('previous_page', $attrPage2['pagination']);
        $this->assertArrayHasKey('previous_page_path', $attrPage2['pagination']);
        $this->assertArrayHasKey('previous_page_url', $attrPage2['pagination']);
        $this->assertArrayHasKey('next_page', $attrPage2['pagination']);
        $this->assertArrayHasKey('next_page_path', $attrPage2['pagination']);
        $this->assertArrayHasKey('next_page_url', $attrPage2['pagination']);

        $this->assertCount(1, $attrPage2['pagination']['items']);
        $this->assertEquals(1, $attrPage2['pagination']['per_page']);
        $this->assertEquals(2, $attrPage2['pagination']['total_items']);
        $this->assertEquals(2, $attrPage2['pagination']['total_pages']);
        $this->assertEquals(2, $attrPage2['pagination']['page']);
        $this->assertEquals(1, $attrPage2['pagination']['previous_page']);
        $this->assertEquals('blog/index.html', $attrPage2['pagination']['previous_page_path']);
        $this->assertEquals('/blog', $attrPage2['pagination']['previous_page_url']);

        $this->assertNull($attrPage2['pagination']['next_page']);
        $this->assertNull($attrPage2['pagination']['next_page_path']);
        $this->assertNull($attrPage2['pagination']['next_page_url']);
    }

    public function testPaginateWithCustomPermalink()
    {
        $content = 'Paginator content';
        $attributes = [
            'site' => [
                'posts' => [
                    '_posts/2015-05-26-hi' => [
                        'content' => 'My content 1',
                    ],
                    '_posts/2015-05-26-welcome' => [
                        'content' => 'My content 2',
                    ],
                ],
            ],
        ];

        $templateItem = new Item($content, 'blog/index.html', ['max_page' => 1, 'permalink' => '/page:num.html']);
        $templateItem->setPath('blog/index.html', Item::SNAPSHOT_PATH_RELATIVE);

        $pageItems = $this->pagination->generateItems($templateItem, $attributes);

        $this->assertTrue(is_array($pageItems));
        $this->assertCount(2, $pageItems);

        $page1 = $pageItems[0];
        $this->assertEquals('blog/page1.html', $page1->getPath(Item::SNAPSHOT_PATH_RELATIVE));
        $this->assertEquals('/blog/page1.html', $page1->getPath(Item::SNAPSHOT_PATH_PERMALINK));

        $page2 = $pageItems[1];
        $this->assertEquals('blog/page2.html', $page2->getPath(Item::SNAPSHOT_PATH_RELATIVE));
        $this->assertEquals('/blog/page2.html', $page2->getPath(Item::SNAPSHOT_PATH_PERMALINK));
    }
}
