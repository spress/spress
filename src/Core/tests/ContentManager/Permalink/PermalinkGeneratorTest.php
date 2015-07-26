<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\ContentManager\Permalink;

use Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkGenerator;
use Yosymfony\Spress\Core\DataSource\Item;

class PermalinkGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testNonePermalink()
    {
        $pmg = new PermalinkGenerator('none');
        $permalink = $pmg->getPermalink($this->createItem('index.html'));

        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkInterface', $permalink);
        $this->assertEquals('index.html', $permalink->getPath());
        $this->assertEquals('/index.html', $permalink->getUrlPath());

        $item = $this->createItem('index.html');
        $item->setCollection('events');
        $permalink = $pmg->getPermalink($item);

        $this->assertEquals('events/index.html', $permalink->getPath());
        $this->assertEquals('/events/index.html', $permalink->getUrlPath());
    }

    public function testDatePermalink()
    {
        $pmg = new PermalinkGenerator('date');
        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'date' => '2015-04-17',
            'title' => 'my-post',
        ]));

        $this->assertEquals('2015/04/17/my-post.html', $permalink->getPath());
        $this->assertEquals('/2015/04/17/my-post.html', $permalink->getUrlPath());

        $item = $this->createItem('index.html', [
            'date' => '2015-04-17',
            'title' => 'my-post',
        ]);
        $item->setCollection('events');

        $permalink = $pmg->getPermalink($item);

        $this->assertEquals('events/2015/04/17/my-post.html', $permalink->getPath());
        $this->assertEquals('/events/2015/04/17/my-post.html', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('my-page.html'));

        $this->assertEquals('my-page.html', $permalink->getPath());
        $this->assertEquals('/my-page.html', $permalink->getUrlPath());
    }

    public function testOrdinalPermalink()
    {
        $pmg = new PermalinkGenerator('ordinal');
        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'date' => '2015-04-01',
            'title' => 'my-post',
        ]));

        $this->assertEquals('2015/1/my-post.html', $permalink->getPath());
        $this->assertEquals('/2015/1/my-post.html', $permalink->getUrlPath());

        $item = $this->createItem('index.html', [
            'date' => '2015-04-17',
            'title' => 'my-post',
        ]);
        $item->setCollection('events');

        $permalink = $pmg->getPermalink($item);

        $this->assertEquals('events/2015/17/my-post.html', $permalink->getPath());
        $this->assertEquals('/events/2015/17/my-post.html', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('my-page.html'));

        $this->assertEquals('my-page.html', $permalink->getPath());
        $this->assertEquals('/my-page.html', $permalink->getUrlPath());
    }

    public function testPrettyPermalink()
    {
        $pmg = new PermalinkGenerator('pretty');
        $permalink = $pmg->getPermalink($this->createItem('index.html'));

        $this->assertEquals('index.html', $permalink->getPath());
        $this->assertEquals('/', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('my-page/index.html'));

        $this->assertEquals('my-page/index.html', $permalink->getPath());
        $this->assertEquals('/my-page', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('my-page.html'));

        $this->assertEquals('my-page/index.html', $permalink->getPath());
        $this->assertEquals('/my-page', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('users.xml'));

        $this->assertEquals('users.xml', $permalink->getPath());
        $this->assertEquals('/users.xml', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('docs/users.xml'));

        $this->assertEquals('docs/users.xml', $permalink->getPath());
        $this->assertEquals('/docs/users.xml', $permalink->getUrlPath());

        $item = $this->createItem('docs/users.xml');
        $item->setCollection('events');

        $permalink = $pmg->getPermalink($item);

        $this->assertEquals('events/docs/users.xml', $permalink->getPath());
        $this->assertEquals('/events/docs/users.xml', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('docs/users.xml', [
            'collection' => 'pages',
        ]));

        $this->assertEquals('docs/users.xml', $permalink->getPath());
        $this->assertEquals('/docs/users.xml', $permalink->getUrlPath());
    }

    public function testPrettyDatePermalink()
    {
        $pmg = new PermalinkGenerator('pretty');
        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'date' => '2015-04-17',
        ]));

        $this->assertEquals('2015/04/17/index.html', $permalink->getPath());
        $this->assertEquals('/2015/04/17', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'date' => '2015-04-17',
            'title' => 'my first post',
        ]));

        $this->assertEquals('2015/04/17/my-first-post/index.html', $permalink->getPath());
        $this->assertEquals('/2015/04/17/my-first-post', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'date' => '2015-04-17',
            'title' => 'my first post',
            'categories' => ['news'],
        ]));

        $this->assertEquals('news/2015/04/17/my-first-post/index.html', $permalink->getPath());
        $this->assertEquals('/news/2015/04/17/my-first-post', $permalink->getUrlPath());
    }

    public function testPermalinkAttribute()
    {
        $pmg = new PermalinkGenerator('pretty');
        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'permalink' => 'none',
            'date' => '2015-04-17',
            'title' => 'my first post',
        ]));

        $this->assertEquals('index.html', $permalink->getPath());
        $this->assertEquals('/index.html', $permalink->getUrlPath());
    }

    public function testCustomPermalink()
    {
        $pmg = new PermalinkGenerator('/my-path/:basename.:extension');
        $permalink = $pmg->getPermalink($this->createItem('index.html'));

        $this->assertEquals('my-path/index.html', $permalink->getPath());
        $this->assertEquals('/my-path/index.html', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'permalink' => '/my-path/:year-:month-:day-:title.:extension',
            'date' => '2015-04-17',
            'title' => 'my first post',
        ]));

        $this->assertEquals('my-path/2015-04-17-my-first-post.html', $permalink->getPath());
        $this->assertEquals('/my-path/2015-04-17-my-first-post.html', $permalink->getUrlPath());
    }

    public function testPreservePathTitle()
    {
        $pmg = new PermalinkGenerator('/:title/index.html', true);
        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'date' => '2015-04-17',
            'title' => 'title post',
            'title_path' => 'first-post',
        ]));

        $this->assertEquals('first-post/index.html', $permalink->getPath());
        $this->assertEquals('/first-post/index.html', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'date' => '2015-04-17',
            'preserve_path_title' => false,
            'title' => 'title post',
            'title_path' => 'first-post',
        ]));

        $this->assertEquals('title-post/index.html', $permalink->getPath());
        $this->assertEquals('/title-post/index.html', $permalink->getUrlPath());
    }

    public function testPermalinkForBinaryItem()
    {
        $pmg = new PermalinkGenerator('pretty');

        $item = $this->createItem('spress.phar', [], true);
        $permalink = $pmg->getPermalink($item);

        $this->assertEquals('spress.phar', $permalink->getPath());
        $this->assertEquals('/spress.phar', $permalink->getUrlPath());

        $item = $this->createItem('/download/spress.phar', [], true);
        $permalink = $pmg->getPermalink($item);

        $this->assertEquals('download/spress.phar', $permalink->getPath());
        $this->assertEquals('/download/spress.phar', $permalink->getUrlPath());
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\AttributeValueException
     */
    public function testPrettyBadTypeForCategoriesAttribute()
    {
        $pmg = new PermalinkGenerator('pretty');
        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'categories' => 'news',
        ]));
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\AttributeValueException
     */
    public function testPrettyBadDateAttribute()
    {
        $pmg = new PermalinkGenerator('pretty');
        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'date' => [],
        ]));
    }

    private function createItem($path, $attributes = [], $binary = false)
    {
        $item = new Item('', $path, $attributes, $binary);
        $item->setPath($path, Item::SNAPSHOT_PATH_RELATIVE);

        return $item;
    }
}
