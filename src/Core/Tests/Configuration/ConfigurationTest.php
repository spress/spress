<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Yosymfony\ConfigLoader\Config;
use Yosymfony\ConfigLoader\Loaders\YamlLoader;
use Yosymfony\Spress\Core\Configuration\Configuration;

class ConfigurationTest extends TestCase
{
    protected $defaulConfiguration;

    public function setUp()
    {
        $this->defaulConfiguration = __DIR__.'/../../config/default.yml';
    }

    public function testLoadConfiguration()
    {
        $locator = new FileLocator([]);
        $configLoader = new Config([new YamlLoader($locator)]);

        $config = new Configuration($configLoader, $this->defaulConfiguration);
        $values = $config->loadConfiguration(__DIR__.'/../fixtures/project');

        $this->assertTrue($values['debug']);
        $this->assertFalse($values['drafts']);
        $this->assertFalse($values['safe']);
        $this->assertEquals('dev', $values['env']);
        $this->assertEquals('UTC', $values['timezone']);
        $this->assertEquals('pretty', $values['permalink']);
        $this->assertEquals('', $values['url']);
        $this->assertFalse($values['preserve_path_title']);
        $this->assertFalse($values['no_html_extension']);

        $this->assertTrue(is_array($values['layout_ext']));
        $this->assertCount(3, $values['layout_ext']);

        $this->assertTrue(is_array($values['collections']));
        $this->assertArrayHasKey('posts', $values['collections']);

        $this->assertTrue(is_array($values['data_sources']));
        $this->assertCount(1, $values['data_sources']);
        $this->assertArrayHasKey('filesystem', $values['data_sources']);
        $this->assertArrayHasKey('class', $values['data_sources']['filesystem']);
        $this->assertArrayHasKey('arguments', $values['data_sources']['filesystem']);
        $this->assertArrayHasKey('source_root', $values['data_sources']['filesystem']['arguments']);
        $this->assertArrayHasKey('text_extensions', $values['data_sources']['filesystem']['arguments']);
    }

    public function testLoadConfigurationWithEnvironmentName()
    {
        $locator = new FileLocator([]);
        $configLoader = new Config([new YamlLoader($locator)]);

        $config = new Configuration($configLoader, $this->defaulConfiguration);
        $values = $config->loadConfiguration(__DIR__.'/../fixtures/project', 'prod');

        $this->assertTrue($values['debug']);
        $this->assertFalse($values['drafts']);
        $this->assertFalse($values['safe']);
        $this->assertEquals('prod', $values['env']);
        $this->assertEquals('UTC', $values['timezone']);
        $this->assertEquals('http://spress.yosymfony.com', $values['url']);
        $this->assertEquals('pretty', $values['permalink']);
        $this->assertFalse($values['preserve_path_title']);
        $this->assertFalse($values['no_html_extension']);

        $this->assertTrue(is_array($values['layout_ext']));
        $this->assertCount(3, $values['layout_ext']);

        $this->assertTrue(is_array($values['collections']));
        $this->assertArrayHasKey('posts', $values['collections']);

        $this->assertTrue(is_array($values['data_sources']));
        $this->assertCount(1, $values['data_sources']);
        $this->assertArrayHasKey('filesystem', $values['data_sources']);
        $this->assertArrayHasKey('class', $values['data_sources']['filesystem']);
        $this->assertArrayHasKey('arguments', $values['data_sources']['filesystem']);
        $this->assertArrayHasKey('source_root', $values['data_sources']['filesystem']['arguments']);
        $this->assertArrayHasKey('text_extensions', $values['data_sources']['filesystem']['arguments']);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Not a Spress site at/
     */
    public function testNotASpressSite()
    {
        $locator = new FileLocator([]);
        $configLoader = new Config([new YamlLoader($locator)]);

        $config = new Configuration($configLoader, $this->defaulConfiguration);
        $config->loadConfiguration(__DIR__.'/../fixtures', '');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Expected a non-empty string as environment name.
     */
    public function testEnvironmentEmpty()
    {
        $locator = new FileLocator([]);
        $configLoader = new Config([new YamlLoader($locator)]);

        $config = new Configuration($configLoader, $this->defaulConfiguration);
        $config->loadConfiguration(__DIR__.'/../fixtures/project', '');
    }
}
