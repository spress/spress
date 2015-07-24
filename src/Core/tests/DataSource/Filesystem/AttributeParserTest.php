<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\DataSource\Filesystem;

use Yosymfony\Spress\Core\DataSource\Filesystem\AttributeParser;

class AttributeParserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAttributesFromYamlString()
    {
        $parser = new AttributeParser();

        $attributes = $parser->getAttributesFromString('layout: default');

        $this->assertEquals('default', $attributes['layout']);
    }

    public function testGetAttributesFromEmptyString()
    {
        $parser = new AttributeParser();

        $attributes = $parser->getAttributesFromString('');

        $this->assertTrue(is_array($attributes));
    }

    public function testGetAttributesFromYamlFrontmatter()
    {
        $parser = new AttributeParser();
        $raw = <<<FRONTMATTER
---
layout: default
---
My content
FRONTMATTER;

        $attributes = $parser->getAttributesFromFrontmatter($raw);
        $content = $parser->getContentFromFrontmatter($raw);

        $this->assertEquals('default', $attributes['layout']);
        $this->assertEquals('My content', $content);
    }

    public function testGetAttributesFromEmptyYamlFrontmatter()
    {
        $parser = new AttributeParser();
        $raw = <<<FRONTMATTER
---
---
My content
FRONTMATTER;

        $attributes = $parser->getAttributesFromFrontmatter($raw);
        $content = $parser->getContentFromFrontmatter($raw);

        $this->assertTrue(is_array($attributes));
        $this->assertEquals('My content', $content);
    }

    public function testGetAttributesFromYamlFrontmatterWithExtraDashed()
    {
        $parser = new AttributeParser();
        $raw = <<<FRONTMATTER
---
layout: default
---
My content---
---
FRONTMATTER;

        $attributes = $parser->getAttributesFromFrontmatter($raw);
        $content = $parser->getContentFromFrontmatter($raw);

        $this->assertEquals('default', $attributes['layout']);
        $this->assertEquals("My content---\n---", $content);
    }

    public function testGetAttributesFromNotFrontmatter()
    {
        $parser = new AttributeParser(AttributeParser::PARSER_JSON);
        $raw = <<<FRONTMATTER
--
--
My content
FRONTMATTER;

        $attributes = $parser->getAttributesFromFrontmatter($raw);
        $content = $parser->getContentFromFrontmatter($raw);

        $this->assertTrue(is_array($attributes));
        $this->assertCount(0, $attributes);
        $this->assertEquals("--\n--\nMy content", $content);
    }

    public function testGetAttributesFromFrontmatterWithCarriageReturn()
    {
        $parser = new AttributeParser();
        $raw = "---\r\nlayout: default\r\n---\r\nMy content";

        $attributes = $parser->getAttributesFromFrontmatter($raw);
        $content = $parser->getContentFromFrontmatter($raw);

        $this->assertEquals('default', $attributes['layout']);
        $this->assertEquals('My content', $content);
    }

    public function testGetAttributesFromJsonString()
    {
        $parser = new AttributeParser(AttributeParser::PARSER_JSON);

        $attributes = $parser->getAttributesFromString('{ "layout": "default" }');

        $this->assertEquals('default', $attributes['layout']);
    }

    public function testGetAttributesFromJsonFrontmatter()
    {
        $parser = new AttributeParser(AttributeParser::PARSER_JSON);
        $raw = <<<FRONTMATTER
---
{
	"layout": "default",
	"page": 5
}
---
My content
FRONTMATTER;

        $attributes = $parser->getAttributesFromFrontmatter($raw);
        $content = $parser->getContentFromFrontmatter($raw);

        $this->assertEquals('default', $attributes['layout']);
        $this->assertEquals(5, $attributes['page']);
        $this->assertEquals('My content', $content);
    }

    public function testGetAttributesFromEmptyJsonFrontmatter()
    {
        $parser = new AttributeParser(AttributeParser::PARSER_JSON);
        $raw = <<<FRONTMATTER
---
---
My content
FRONTMATTER;

        $attributes = $parser->getAttributesFromFrontmatter($raw);
        $content = $parser->getContentFromFrontmatter($raw);

        $this->assertTrue(is_array($attributes));
        $this->assertEquals('My content', $content);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetAttributesFromNotFoundParserType()
    {
        $parser = new AttributeParser('ini');
    }
}
