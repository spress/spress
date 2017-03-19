<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\Support;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\Support\FileInfo;

class FileInfoTest extends TestCase
{
    private $predefinedExtensions;

    public function setUp()
    {
        $this->predefinedExtensions = ['html.twig', 'md'];
    }

    public function testGetExtension()
    {
        $fileInfo = new FileInfo('foo.yml');

        $this->assertEquals('yml', $fileInfo->getExtension());
    }

    public function testGetCompoundExtension()
    {
        $fileInfo = new FileInfo('foo.html.twig', $this->predefinedExtensions);

        $this->assertEquals('html.twig', $fileInfo->getExtension());
    }

    public function testGetFilename()
    {
        $fileInfo = new FileInfo('foo.html.twig', $this->predefinedExtensions);

        $this->assertEquals('foo', $fileInfo->getFilename());
    }

    public function testGetFilenameWithDotsInside()
    {
        $fileInfo = new FileInfo('2016-02-02-spress-2.1.1-released.md', $this->predefinedExtensions);

        $this->assertEquals('2016-02-02-spress-2.1.1-released', $fileInfo->getFilename());
    }

    public function testHasPredefinedExtension()
    {
        $fileInfo = new FileInfo('2016-02-02-spress-2.1.1-released.md', $this->predefinedExtensions);

        $this->assertTrue($fileInfo->hasPredefinedExtension());

        $fileInfo = new FileInfo('2016-02-02-spress-2.1.1-released.acme', $this->predefinedExtensions);

        $this->assertFalse($fileInfo->hasPredefinedExtension());
    }
}
