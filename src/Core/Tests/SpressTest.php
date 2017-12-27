<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\ContentManager\ContentManager;
use Yosymfony\Spress\Core\DataWriter\MemoryDataWriter;
use Yosymfony\Spress\Core\SiteMetadata\MemoryMetadata;
use Yosymfony\Spress\Core\Spress;

class SpressTest extends TestCase
{
    private $spress;
    private $contentManagerMock;

    public function setUp()
    {
        $this->contentManagerMock = $this->getMockBuilder(ContentManager::class)
            ->setMethods(['parseSite'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->spress = new Spress();
        $this->spress['spress.config.values'] = [
            'drafts' => false,
            'safe' => false,
            'timezone' => 'UTC'
        ];
        $this->spress['spress.siteMetadata'] =  new MemoryMetadata();
        $this->spress['spress.cms.contentManager'] = $this->contentManagerMock;
    }

    public function testParseMustParseASite()
    {
        $this->contentManagerMock->expects($this->once())
            ->method('parseSite');

        $this->spress->parse();
    }

    public function testParseMustParseASiteWithoutInvokePluginsWhenSafeModeIsEnabled()
    {
        $this->contentManagerMock->expects($this->once())
            ->method('parseSite')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                true,
                $this->anything()
            );
        $configValues = $this->spress['spress.config.values'];
        $configValues['safe'] = true;
        $this->spress['spress.config.values'] = $configValues;

        $this->spress->parse();
    }

    public function testParseMustParseASiteWithDrafts()
    {
        $this->contentManagerMock->expects($this->once())
            ->method('parseSite')
            ->with(
                $this->anything(),
                $this->anything(),
                true,
                $this->anything(),
                $this->anything()
            );
        $configValues = $this->spress['spress.config.values'];
        $configValues['drafts'] = true;
        $this->spress['spress.config.values'] = $configValues;

        $this->spress->parse();
    }

    public function testParseMustParseASiteConsideringTimezone()
    {
        $expectedTimezone = 'Europe/Madrid';
        $this->contentManagerMock->expects($this->once())
            ->method('parseSite')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $expectedTimezone
            );
        $configValues = $this->spress['spress.config.values'];
        $configValues['timezone'] = $expectedTimezone;
        $this->spress['spress.config.values'] = $configValues;

        $this->spress->parse();
    }
}
