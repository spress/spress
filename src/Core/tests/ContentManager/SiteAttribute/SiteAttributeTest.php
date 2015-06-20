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
}
