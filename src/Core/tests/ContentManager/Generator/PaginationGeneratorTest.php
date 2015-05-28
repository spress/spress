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

        $page2 = $pageItems[1];
        $this->assertEquals('blog/page2/index.html', $page2->getPath(Item::SNAPSHOT_PATH_RELATIVE));
        $this->assertEquals('/blog/page2', $page2->getPath(Item::SNAPSHOT_PATH_PERMALINK));
        $this->assertEquals('Paginator content', $page1->getContent());
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
