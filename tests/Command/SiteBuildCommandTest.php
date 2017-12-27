<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Yosymfony\Spress\Console\Application;
use Yosymfony\Spress\Core\Spress;

class SiteBuildCommandTest extends TestCase
{
    protected $spressMock;
    protected $appMock;

    public function setUp()
    {
        $this->spressMock = $this->getMockBuilder(Spress::class)
            ->setMethods(['parse'])
            ->getMock();
        $this->spressMock->method('parse')
            ->willReturn([]);
        $this->spressMock['spress.config.values'] = [
            'host' => '0.0.0.0',
            'port' => 4000,
            'server_watch_ext' => [],
            'parsedown_activated' => true,
            'map_converter_extension' => [],
            'markdown_ext' => [],
            'env' => 'dev',
            'drafts' => false,
            'safe' => false,
            'timezone' => 'UTC',
            'debug' => false,
        ];

        $this->appMock = $this->getMockBuilder(Application::class)
            ->setMethods(['getSpress'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->appMock->method('getSpress')
            ->willReturn($this->spressMock);
        $this->appMock->registerStandardCommands();
    }

    public function testExecuteMustParseASite()
    {
        $this->spressMock->expects($this->once())
            ->method('parse');

        $this->executeSiteBuildCommand();
    }

    public function testExecuteMustParseASiteLocatedBySourceParam()
    {
        $expectedDir = realpath(__DIR__.'/../../src/Core/Tests/fixtures/project');

        $this->appMock->expects($this->once())
            ->method('getSpress')
            ->with($expectedDir);
        $this->spressMock->expects($this->once())
            ->method('parse');

        $this->executeSiteBuildCommand([
            '--source' => $expectedDir,
        ]);
    }

    public function testExecuteMustParseASiteWithDraftsWhenDraftsOptionIsPassed()
    {
        $this->spressMock->expects($this->once())
            ->method('parse');

        $this->executeSiteBuildCommand([
            '--drafts' => true,
        ]);

        $this->assertTrue($this->spressMock['spress.config.drafts']);
    }

    public function testCommandMustParseASiteWithPluginsDisabledWhenSafeOptionIsPassed()
    {
        $this->spressMock->expects($this->once())
            ->method('parse');

        $this->executeSiteBuildCommand([
            '--safe' => true,
        ]);

        $this->assertTrue($this->spressMock['spress.config.safe']);
    }

    public function testExecuteMustParseASiteUsingEnvironmentParamsWhenEnvOptionIsPassed()
    {
        $this->spressMock->expects($this->once())
            ->method('parse');

        $this->executeSiteBuildCommand([
            '--env' => 'prod',
        ]);

        $this->assertEquals('prod', $this->spressMock['spress.config.env']);
    }

    public function testExecuteMustParseASiteUsingParsedownWhenFeatureIsEnabled()
    {
        $configValues = $this->spressMock['spress.config.values'];
        $configValues['parsedown_activated'] = true;
        $this->spressMock['spress.config.values'] = $configValues;

        $this->executeSiteBuildCommand();

        $predefinedConverters = $this->spressMock['spress.cms.converterManager.converters'];

        $this->assertArrayHasKey('ParsedownConverter', $predefinedConverters);
        $this->assertArrayNotHasKey('MichelfMarkdownConverter', $predefinedConverters);
    }

    private function executeSiteBuildCommand(array $params = [])
    {
        $command = $this->appMock->find('site:build');
        $commandTester = new CommandTester($command);
        $commandTester->execute($params, ['decorated' => false]);
    }
}
