<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\ContentManager\SiteAttribute;

use Yosymfony\Spress\Core\ContentManager\SiteAttribute\SiteAttribute;
use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\Support\SupportFacade;

class SiteAttributeTest extends \PHPUnit_Framework_TestCase
{
    public function testSiteAttributes()
    {
        $site = new SiteAttribute(new SupportFacade());
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
        $site = new SiteAttribute(new SupportFacade());
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
    }

    public function testSetItem()
    {
        $site = new SiteAttribute(new SupportFacade());
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
}
