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

        $arr = $site->getAttributes();

        $this->assertArrayHasKey('spress', $arr);
        $this->assertArrayHasKey('site', $arr);
        $this->assertArrayHasKey('page', $arr);
        $this->assertArrayHasKey('time', $arr['site']);
        $this->assertArrayHasKey('name', $arr['site']);
        $this->assertEquals('A Spress site', $arr['site']['name']);
        $this->assertArrayHasKey('collections', $arr['site']);
        $this->assertArrayHasKey('categories', $arr['site']);
        $this->assertArrayHasKey('tags', $arr['site']);
    }
}
