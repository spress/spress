<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\ContentManager\Generator\Taxonomy;

use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\ContentManager\Generator\Taxonomy\TaxonomyGenerator;

class TaxonomyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $taxonomy;

    public function setUp()
    {
        $this->taxonomy = new TaxonomyGenerator();
    }

    public function testTaxonomyWithDefaulValues()
    {
        $post1 = new Item('Post 1', 'posts/2015-05-26-new-release', ['categories' => ['NEWS', 'releases']]);
        $post2 = new Item('Post 2', 'posts/2015-05-26-new-feature', ['categories' => ['news', '']]);

        $collections = [
            'posts' => [$post1, $post2],
        ];

        $templateItem = new Item('Categories content', 'categories/index.html', ['max_page' => 1]);
        $templateItem->setPath('categories/index.html', Item::SNAPSHOT_PATH_RELATIVE);

        $catetoriesItems = $this->taxonomy->generateItems($templateItem, $collections);

        $this->assertTrue(is_array($catetoriesItems));
        $this->assertCount(3, $catetoriesItems);
        $this->assertContainsOnly('\Yosymfony\Spress\Core\DataSource\ItemInterface', $catetoriesItems);

        $item = $catetoriesItems[0];

        $this->assertArrayHasKey('pagination', $item->getAttributes());
        $this->assertArrayHasKey('term', $item->getAttributes());
        $this->assertEquals('news', $item->getAttributes()['term']);
        $this->assertEquals('categories/news/index.html', $item->getPath(Item::SNAPSHOT_PATH_RELATIVE));
        $this->assertEquals('/categories/news', $item->getPath(Item::SNAPSHOT_PATH_PERMALINK));
        $this->assertEquals('Categories content', $item->getContent());

        $item = $catetoriesItems[1];

        $this->assertArrayHasKey('pagination', $item->getAttributes());
        $this->assertArrayHasKey('term', $item->getAttributes());
        $this->assertEquals('news', $item->getAttributes()['term']);
        $this->assertEquals('categories/news/page2/index.html', $item->getPath(Item::SNAPSHOT_PATH_RELATIVE));
        $this->assertEquals('/categories/news/page2', $item->getPath(Item::SNAPSHOT_PATH_PERMALINK));
        $this->assertEquals('Categories content', $item->getContent());

        $item = $catetoriesItems[2];

        $this->assertArrayHasKey('pagination', $item->getAttributes());
        $this->assertArrayHasKey('term', $item->getAttributes());
        $this->assertEquals('releases', $item->getAttributes()['term']);
        $this->assertEquals('categories/releases/index.html', $item->getPath(Item::SNAPSHOT_PATH_RELATIVE));
        $this->assertEquals('/categories/releases', $item->getPath(Item::SNAPSHOT_PATH_PERMALINK));
        $this->assertEquals('Categories content', $item->getContent());

        $attributes = $post1->getAttributes();
        $this->assertArrayHasKey('terms_url', $attributes);
        $this->assertArrayHasKey('categories', $attributes['terms_url']);
        $this->assertCount(2, $attributes['terms_url']['categories']);
        $this->assertEquals('/categories/news', $attributes['terms_url']['categories']['news']);
        $this->assertEquals('/categories/releases', $attributes['terms_url']['categories']['releases']);
    }

    public function testTaxonomiesPointingToSameSlugedTerm()
    {
        $post1 = new Item('Post 1', 'posts/2015-05-26-new-release', ['categories' => ['баш']]);
        $post2 = new Item('Post 2', 'posts/2015-05-27-new-feature', ['categories' => ['баШ']]);
        $post3 = new Item('Post 3', 'posts/2015-05-28-new-feature', ['categories' => ['bash']]);

        $collections = [
            'posts' => [$post1, $post2, $post3],
        ];

        $templateItem = new Item('Categories content', 'categories/index.html', ['max_page' => 3]);
        $templateItem->setPath('categories/index.html', Item::SNAPSHOT_PATH_RELATIVE);

        $catetoriesItems = $this->taxonomy->generateItems($templateItem, $collections);

        $this->assertTrue(is_array($catetoriesItems));
        $this->assertCount(1, $catetoriesItems);

        $item = $catetoriesItems[0];

        $this->assertArrayHasKey('pagination', $item->getAttributes());
        $this->assertArrayHasKey('term', $item->getAttributes());
        $this->assertEquals('баш', $item->getAttributes()['term']);
        $this->assertEquals('categories/bash/index.html', $item->getPath(Item::SNAPSHOT_PATH_RELATIVE));

        $attributes = $post1->getAttributes();
        $this->assertArrayHasKey('terms_url', $attributes);
        $this->assertArrayHasKey('categories', $attributes['terms_url']);
        $this->assertCount(3, $attributes['terms_url']['categories']);
        $this->assertEquals('/categories/bash', $attributes['terms_url']['categories']['bash']);
        $this->assertEquals('/categories/bash', $attributes['terms_url']['categories']['баШ']);
        $this->assertEquals('/categories/bash', $attributes['terms_url']['categories']['баш']);
    }
}
