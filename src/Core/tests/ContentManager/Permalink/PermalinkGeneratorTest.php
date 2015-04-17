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
    public function testPathPermalink()
    {
        $pmg = new PermalinkGenerator('path');
        $permalink = $pmg->getPermalink($this->createItem('index.html'));

        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkInterface', $permalink);
        $this->assertEquals('index.html', $permalink->getPath());
        $this->assertEquals('/index.html', $permalink->getUrlPath());
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
    }

    public function testPrettyDatePermalink()
    {
        $pmg = new PermalinkGenerator('pretty');
        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'date' => '2015-04-17'
        ]));

        $this->assertEquals('2015/04/17/index.html', $permalink->getPath());
        $this->assertEquals('/2015/04/17', $permalink->getUrlPath());

        $permalink = $pmg->getPermalink($this->createItem('index.html', [
            'date' => '2015-04-17',
            'title' => 'my first post'
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

    private function createItem($path, $attributes = [])
    {
        $item = new Item('', $path, $attributes);
        $item->setPath($path);

        return $item;
    }
}
