<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\ContentManager\SiteAttribute;

use Yosymfony\Spress\Core\ContentManager\SiteAttribute\SiteAttribute;
use Yosymfony\Spress\Core\DataSource\Item;

class SiteAttributeTest extends \PHPUnit_Framework_TestCase
{
    public function testSiteAttributes()
    {
        $site = new SiteAttribute();
        $arr = $site->getAttributes();

        $this->assertArrayHasKey('spress', $arr);
        $this->assertArrayHasKey('site', $arr);
        $this->assertArrayHasKey('page', $arr);
        $this->assertArrayHasKey('time', $arr['site']);
        $this->assertArrayHasKey('collections', $arr['site']);
        $this->assertArrayHasKey('categories', $arr['site']);
        $this->assertArrayHasKey('tags', $arr['site']);
    }

    public function testMergedAttributes()
    {
        $site = new SiteAttribute();
        $site->initialize([
            'name' => 'A Spress site',
        ]);
        $site->addAttribute('site.author', 'Yo! Symfony');

        $this->assertTrue($site->hasAttribute('site.name'));
        $this->assertTrue($site->hasAttribute('site.author'));
        $this->assertEquals('Yo! Symfony', $site->getAttribute('site.author'));

        $site->addAttribute('site.author', 'Yo! Symfony 2');
        $this->assertEquals('Yo! Symfony', $site->getAttribute('site.author'));

        $site->setAttribute('site.author', 'Yo! Symfony 2');
        $this->assertEquals('Yo! Symfony 2', $site->getAttribute('site.author'));

        $arr = $site->getAttributes();

        $this->assertTrue(is_array($arr));
        $this->assertArrayHasKey('spress', $arr);
        $this->assertArrayHasKey('site', $arr);
        $this->assertArrayHasKey('page', $arr);
        $this->assertArrayHasKey('time', $arr['site']);
        $this->assertArrayHasKey('name', $arr['site']);
        $this->assertArrayHasKey('collections', $arr['site']);
        $this->assertArrayHasKey('categories', $arr['site']);
        $this->assertArrayHasKey('tags', $arr['site']);

        $this->assertEquals('A Spress site', $arr['site']['name']);

        $site->initialize();

        $this->assertFalse($site->hasAttribute('site.name'));
    }

    public function testSetItem()
    {
        $site = new SiteAttribute();
        $site->initialize();

        $item = new Item('The content', 'index.html', ['collection' => 'pages', 'title' => 'Welcome']);
        $item->setPath('index.html', Item::SNAPSHOT_PATH_RELATIVE);
        $site->setItem($item);

        $arr = $site->getAttributes();

        $this->assertTrue(is_array($arr));

        $this->assertArrayHasKey('page', $arr);
        $this->assertArrayHasKey('pages', $arr['site']);
        $this->assertArrayHasKey('index.html', $arr['site']['pages']);
        $this->assertArrayHasKey('collections', $arr['site']);
        $this->assertArrayHasKey('categories', $arr['site']);
        $this->assertArrayHasKey('tags', $arr['site']);
        $this->assertArrayHasKey('collection', $arr['page']);
        $this->assertArrayHasKey('title', $arr['page']);
        $this->assertArrayHasKey('id', $arr['page']);
        $this->assertArrayHasKey('path', $arr['page']);

        $this->assertCount(1, $arr['site']['pages']);
        $this->assertCount(0, $arr['site']['collections']);
        $this->assertCount(0, $arr['site']['categories']);
        $this->assertCount(0, $arr['site']['tags']);

        $this->assertEquals('pages', $arr['site']['pages']['index.html']['collection']);
        $this->assertEquals('Welcome', $arr['site']['pages']['index.html']['title']);
        $this->assertEquals('index.html', $arr['site']['pages']['index.html']['id']);
        $this->assertEquals('index.html', $arr['site']['pages']['index.html']['path']);

        $this->assertEquals('pages', $arr['page']['collection']);
        $this->assertEquals('Welcome', $arr['page']['title']);
        $this->assertEquals('index.html', $arr['page']['id']);
        $this->assertEquals('index.html', $arr['page']['path']);
    }

    public function testSetItemWithRelationships()
    {
        $site = new SiteAttribute();
        $site->initialize();

        $item = new Item('The content', 'post1.md');
        $item->setPath('post1.html', Item::SNAPSHOT_PATH_RELATIVE);
        $item->getRelationshipCollection()->add('next', new Item('The content 2', 'post2.md'));

        $site->setItem($item);

        $siteAttributes = $site->getAttributes();

        $this->assertArrayHasKey('relationships', $siteAttributes['site']['pages']['post1.md']);

        $relationships = $siteAttributes['site']['pages']['post1.md']['relationships'];

        $this->assertCount(1, $relationships);

        $this->assertArrayHasKey('next', $relationships);
        $this->assertEquals('post2.md', $relationships['next'][0]['id']);
    }

    public function testSetItemPostCollection()
    {
        $site = new SiteAttribute();
        $site->initialize();

        $item = new Item('The content', 'posts/2015-06-22-hi.md', [
            'title' => 'Welcome',
            'categories' => ['news'],
            'tags' => ['release'],
        ]);
        $item->setCollection('posts');
        $item->setPath('2015/06/22/welcome/index.html', Item::SNAPSHOT_PATH_RELATIVE);
        $site->setItem($item);

        $arr = $site->getAttributes();

        $this->assertTrue(is_array($arr));

        $this->assertArrayHasKey('page', $arr);
        $this->assertArrayHasKey('categories', $arr['page']);
        $this->assertArrayHasKey('tags', $arr['page']);
        $this->assertArrayHasKey('posts', $arr['site']);
        $this->assertArrayHasKey('news', $arr['site']['categories']);
        $this->assertArrayHasKey('release', $arr['site']['tags']);
        $this->assertArrayHasKey('posts/2015-06-22-hi.md', $arr['site']['posts']);
        $this->assertArrayHasKey('posts/2015-06-22-hi.md', $arr['site']['categories']['news']);
        $this->assertArrayHasKey('posts/2015-06-22-hi.md', $arr['site']['tags']['release']);
        $this->assertArrayHasKey('collections', $arr['site']);
        $this->assertArrayHasKey('categories', $arr['site']);
        $this->assertArrayHasKey('tags', $arr['site']);
        $this->assertArrayHasKey('collection', $arr['page']);
        $this->assertArrayHasKey('title', $arr['page']);
        $this->assertArrayHasKey('id', $arr['page']);
        $this->assertArrayHasKey('path', $arr['page']);

        $this->assertCount(1, $arr['site']['posts']);
        $this->assertCount(0, $arr['site']['collections']);
        $this->assertCount(1, $arr['site']['categories']);
        $this->assertCount(1, $arr['site']['tags']);

        $this->assertEquals('posts', $arr['site']['posts']['posts/2015-06-22-hi.md']['collection']);
        $this->assertEquals('Welcome', $arr['site']['posts']['posts/2015-06-22-hi.md']['title']);
        $this->assertEquals('posts/2015-06-22-hi.md', $arr['site']['posts']['posts/2015-06-22-hi.md']['id']);
        $this->assertEquals('2015/06/22/welcome/index.html', $arr['site']['posts']['posts/2015-06-22-hi.md']['path']);

        $this->assertEquals('posts', $arr['site']['categories']['news']['posts/2015-06-22-hi.md']['collection']);
        $this->assertEquals('Welcome', $arr['site']['categories']['news']['posts/2015-06-22-hi.md']['title']);
        $this->assertEquals('posts/2015-06-22-hi.md', $arr['site']['categories']['news']['posts/2015-06-22-hi.md']['id']);
        $this->assertEquals('2015/06/22/welcome/index.html',
            $arr['site']['categories']['news']['posts/2015-06-22-hi.md']['path']);

        $this->assertEquals('posts', $arr['site']['tags']['release']['posts/2015-06-22-hi.md']['collection']);
        $this->assertEquals('Welcome', $arr['site']['tags']['release']['posts/2015-06-22-hi.md']['title']);
        $this->assertEquals('posts/2015-06-22-hi.md', $arr['site']['tags']['release']['posts/2015-06-22-hi.md']['id']);
        $this->assertEquals('2015/06/22/welcome/index.html',
            $arr['site']['tags']['release']['posts/2015-06-22-hi.md']['path']);

        $this->assertEquals('posts', $arr['page']['collection']);
        $this->assertEquals('Welcome', $arr['page']['title']);
        $this->assertEquals('posts/2015-06-22-hi.md', $arr['page']['id']);
        $this->assertEquals('2015/06/22/welcome/index.html', $arr['page']['path']);

        $this->assertCount(1, $arr['page']['categories']);
        $this->assertCount(1, $arr['page']['tags']);
    }
}
