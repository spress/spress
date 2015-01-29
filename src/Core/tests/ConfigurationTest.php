<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests;

use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Core\Application;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $config;

    public function setUp()
    {
        $this->app = new Application();
        $this->config = $this->app['spress.config'];
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove(__DIR__.'/fixtures/project/_site');
    }

    public function testConfiguration()
    {
        $this->assertInstanceOf('Yosymfony\Spress\Core\Configuration', $this->app['spress.config']);
    }

    public function testLoadConfigurations()
    {
        $this->config->loadLocal(__DIR__.'/fixtures/project');

        $this->assertGreaterThan(0, count($this->config->getRepository()));
        $this->assertGreaterThan(0, count($this->config->getGlobal()));
        $this->assertCount(2, $this->config->getLocal());
        $this->assertCount(0, $this->config->getEnvironment());
    }

    public function testLoadProductionConfiguration()
    {
        $this->config->loadLocal(__DIR__.'/fixtures/project', 'prod');
        $environmentRepository = $this->config->getEnvironment();
        $repository = $this->config->getRepository();

        $this->assertCount(1, $environmentRepository);
        $this->assertEquals('http://spress.yosymfony.com', $environmentRepository['url']);
        $this->assertEquals('http://spress.yosymfony.com', $repository['url']);
    }

    public function testConfigurationInline()
    {
        $repositoryA = $this->config->getRepositoryInline('template: template-inline-test');
        $repositoryC = $repositoryA->union($this->config->getRepository());

        $this->assertEquals('template-inline-test', $repositoryC['template']);
    }

    public function testCreateBlankRepository()
    {
        $repository = $this->config->createBlankRepository();

        $this->assertInstanceOf('Yosymfony\ConfigLoader\Repository', $repository);
        $this->assertCount(0, $repository);
    }

    public function testConfigurationInlineEmpty()
    {
        $repository = $this->config->getRepositoryInline('');

        $this->assertCount(0, $repository);
    }

    public function testConfigurationInlineNull()
    {
        $repository = $this->config->getRepositoryInline(null);

        $this->assertCount(0, $repository);
    }

    public function testGetConfigFilename()
    {
        $this->assertEquals('config.yml', $this->config->GetConfigFilename());
    }

    public function testGetConfigEnvironmentDevFilename()
    {
        $this->config->loadLocal(__DIR__.'/fixtures/project');

        $this->assertNull($this->config->getConfigEnvironmentFilename());
    }

    public function testGetConfigEnvironmentDevExplicitFilename()
    {
        $this->config->loadLocal(__DIR__.'/fixtures/project', 'dev');

        $this->assertNull($this->config->getConfigEnvironmentFilename());
    }

    public function testGetConfigEnvironmentProdFilename()
    {
        $this->config->loadLocal(__DIR__.'/fixtures/project', 'prod');

        $this->assertEquals('config_prod.yml', $this->config->getConfigEnvironmentFilename());
        $this->assertEquals('prod', $this->config->getEnvironmentName());
    }

    public function testGetConfigEnvironmentFilenameWildcard()
    {
        $this->assertEquals('config_*.yml', $this->config->getConfigEnvironmentFilenameWildcard());
    }

    public function testGetDefaultEnvironmentName()
    {
        $this->assertEquals('dev', $this->config->getEnvironmentName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadLocalConfigurationFail()
    {
        $this->config->loadLocal('/not/exists/directory/config.yml');
    }
}
