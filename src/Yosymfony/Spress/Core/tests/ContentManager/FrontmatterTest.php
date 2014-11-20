<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\ContentManager;

use Yosymfony\Spress\Core\Application;
use Yosymfony\Spress\Core\ContentManager\Frontmatter;

class FrontmatterTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $configuration;

    public function setUp()
    {
        $this->app = new Application();
        $this->configuration = $this->app['spress.config'];
    }

    public function testFrontmatter()
    {
        $conf = <<<EOT
title: My firts post
categories: [miscellany]
tags: [post, example]
EOT;
        $content = "---\n".$conf."\n---\nYour post content";
        $fm = new Frontmatter($content, $this->configuration);

        $this->assertTrue($fm->hasFrontmatter());
        $this->assertNotNull($fm->getFrontmatter());
        $this->assertCount(3, $fm->getFrontmatterArray());
        $this->assertGreaterThan(0, strlen($fm->getFrontmatterString()));
        $this->assertEquals('Your post content', $fm->getContentNotFrontmatter());
        $this->assertStringStartsWith('---', $fm->getFrontmatterWithDashedLines());
        $this->assertStringEndsWith('---', $fm->getFrontmatterWithDashedLines());
    }

    public function testFrontmatterWithCarriageReturn()
    {
        $conf = 'title: My firts post';
        $content = "---\r\n".$conf."\r\n---\r\nYour post content";
        $fm = new Frontmatter($content, $this->configuration);

        $this->assertTrue($fm->hasFrontmatter());
        $this->assertNotNull($fm->getFrontmatter());
        $this->assertCount(1, $fm->getFrontmatterArray());
        $this->assertGreaterThan(0, strlen($fm->getFrontmatterString()));
        $this->assertEquals('Your post content', $fm->getContentNotFrontmatter());
        $this->assertStringStartsWith('---', $fm->getFrontmatterWithDashedLines());
        $this->assertStringEndsWith('---', $fm->getFrontmatterWithDashedLines());
    }

    public function testNotFrontmatter()
    {
        $conf = 'title: My firts post';
        $content = "--\n".$conf."\n--\nYour post content";
        $fm = new Frontmatter($content, $this->configuration);

        $this->assertFalse($fm->hasFrontmatter());
        $this->assertFalse($fm->getFrontmatterWithDashedLines());
        $this->assertNotNull($fm->getFrontmatter());
        $this->assertFalse($fm->getFrontmatterString());
        $this->assertCount(0, $fm->getFrontmatterArray());
        $this->assertEquals($content, $fm->getContentNotFrontmatter());
    }

    public function testFrontmatterEmpty()
    {
        $content = "---\n---\nYour post content";
        $fm = new Frontmatter($content, $this->configuration);

        $this->assertTrue($fm->hasFrontmatter());
        $this->assertStringStartsWith('---', $fm->getFrontmatterWithDashedLines());
        $this->assertStringEndsWith('---', $fm->getFrontmatterWithDashedLines());
        $this->assertNotNull($fm->getFrontmatter());
        $this->assertCount(0, $fm->getFrontmatterArray());
        $this->assertEquals('', $fm->getFrontmatterString());
        $this->assertEquals('Your post content', $fm->getContentNotFrontmatter());
    }

    public function testSetFrontmatter()
    {
        $content = "Your post content";
        $fm = new Frontmatter($content, $this->configuration);
        $fm->setFrontmatter(['key1' => 1, 'key2' => 'value']);

        $this->assertTrue($fm->hasFrontmatter());
        $this->assertNotNull($fm->getFrontmatter());
        $this->assertCount(2, $fm->getFrontmatterArray());
        $this->assertStringStartsWith('---', $fm->getFrontmatterWithDashedLines());
        $this->assertStringEndsWith('---', $fm->getFrontmatterWithDashedLines());
        $this->assertEquals('', $fm->getFrontmatterString());
        $this->assertEquals('Your post content', $fm->getContentNotFrontmatter());
    }

    public function testMoreLineDashed()
    {
        $content = "---\nlayout: default\n---\nYour post content-----------------";
        $fm = new Frontmatter($content, $this->configuration);

        $this->assertTrue($fm->hasFrontmatter());
        $this->assertStringStartsWith('---', $fm->getFrontmatterWithDashedLines());
        $this->assertStringEndsWith('---', $fm->getFrontmatterWithDashedLines());
        $this->assertNotNull($fm->getFrontmatter());
        $this->assertCount(1, $fm->getFrontmatterArray());
        $this->assertEquals('Your post content-----------------', $fm->getContentNotFrontmatter());
    }
}
