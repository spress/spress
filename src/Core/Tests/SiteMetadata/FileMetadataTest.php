<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\SiteMetadata;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\SiteMetadata\FileMetadata;
use Yosymfony\Spress\Core\Support\Filesystem;

class FileMetadataTest extends TestCase
{
    public function testLoadMustLoadASiteMetadataFile()
    {
        $json = <<<'json'
        {
            "generator": {
                "name": "spress"
            }
        }
json;
        $fsStub = $this->createFilesystemStubReadFile($json);
        $fileMetadata = new FileMetadata('/acme/spress.metadata', $fsStub);

        $fileMetadata->load();

        $this->assertEquals('spress', $fileMetadata->get('generator', 'name'));
    }

    /**
     * @expectedException Yosymfony\Spress\Core\SiteMetadata\Exception\InvalidSiteMetadataException
     * @expectedExceptionMessage Error parsing JSON - "Syntax error, malformed JSON". File: "/acme/spress.metadata"
     */
    public function testLoadMustFailWhenMalformedJson()
    {
        $fsStub = $this->createFilesystemStubReadFile('{ "generator": }');
        $fileMetadata = new FileMetadata('/acme/spress.metadata', $fsStub);

        $fileMetadata->load();
    }

    /**
     * @expectedException Yosymfony\Spress\Core\SiteMetadata\Exception\LoadSiteMetadataException
     * @expectedExceptionMessage Error loading the site metadata: IO error.
     */
    public function testLoadMustFailWhenThereIsAnErrorLoadingASiteMetadataFile()
    {
        $fsStub = $this->createMock(Filesystem::class);
        $fsStub->method('readFile')
            ->will($this->throwException(new \Exception('IO error.')));

        $fileMetadata = new FileMetadata('/acme/spress.metadata', $fsStub);

        $fileMetadata->load();
    }

    public function testSaveMustSaveASiteMetadataFile()
    {
        $expectedFilename = '/acme/spress.metadata';
        $expectedJson = <<<'json'
{
    "generator": {
        "name": "spress"
    }
}
json;
        $fsMock = $this->createFilesystemMockDumpFile($expectedFilename, $expectedJson);
        $fileMetadata = new FileMetadata($expectedFilename, $fsMock);

        $fileMetadata->set('generator', 'name', 'spress');
        $fileMetadata->save();
    }

    /**
     * @expectedException Yosymfony\Spress\Core\SiteMetadata\Exception\SaveSiteMetadataException
     * @expectedExceptionMessage Error saving the site metadata: IO error.
     */
    public function testSaveMustFailWhenThereIsAnErrorSavingASiteMetadataFile()
    {
        $fsStub = $this->createMock(Filesystem::class);
        $fsStub->method('dumpFile')
            ->will($this->throwException(new \Exception('IO error.')));
        $fileMetadata = new FileMetadata('/acme/spress.metadata', $fsStub);

        $fileMetadata->save();
    }

    private function createFilesystemStubReadFile($fileContent)
    {
        $fsStub = $this->createMock(Filesystem::class);
        $fsStub->method('readFile')
            ->willReturn($fileContent);
        $fsStub->method('exists')
            ->willReturn(true);

        return $fsStub;
    }

    private function createFilesystemMockDumpFile($expectedFilename, $expectedContent)
    {
        $fsMock = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['dumpFile'])
            ->getMock();
        $fsMock->expects($this->once())
            ->method('dumpFile')
            ->with(
                $this->equalTo($expectedFilename),
                $this->equalTo($expectedContent)
            );

        return $fsMock;
    }
}
