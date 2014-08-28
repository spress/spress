<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\ContentLocator;

use Symfony\Component\Finder\SplFileInfo;
use Yosymfony\Spress\Core\ContentLocator\FileItem;

class FileItemTest extends \PHPUnit_Framework_TestCase
{
    protected $mock;
    protected $mock2;

    public function setUp()
    {
        $this->mock = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->setMethods(array(
                'getContents',
                'getRealpath',
            ))
            ->setConstructorArgs(array(
                '/spress_test/_posts/world/2013-08-22-example.md',
                'world',
                'world/2013-08-22-example.md',
            ))
            ->getMock();

        $this->mock->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue('mock content'));

        $this->mock->expects($this->any())
            ->method('getRealpath')
            ->will($this->returnValue('/spress_test/_posts/world/2013-08-22-example.md'));
    }

    public function testType()
    {
        $fileItem = new FileItem($this->mock, FileItem::TYPE_POST);

        $this->assertEquals(FileItem::TYPE_POST, $fileItem->getType());
    }

    public function testPathInfo()
    {
        $fileItem = new FileItem($this->mock, FileItem::TYPE_POST);

        $this->assertEquals('world', $fileItem->getRelativePath());
        $this->assertEquals('2013-08-22-example.md', $fileItem->getFileName());
        $this->assertEquals('2013-08-22-example', $fileItem->getFileName(false));
        $this->assertEquals('md', $fileItem->getExtension());
    }

    public function testRelativePathExplode()
    {
        $fileItem = new FileItem($this->mock, FileItem::TYPE_POST);
        $categories = $fileItem->getRelativePathExplode();

        $this->assertTrue(is_array($categories));
        $this->assertCount(1, $categories);
        $this->assertEquals('world', $categories[0]);
    }

    public function testGetSourceContent()
    {
        $fileItem = new FileItem($this->mock, FileItem::TYPE_POST);

        $this->assertEquals('mock content', $fileItem->getSourceContent());
        $this->assertEquals('mock content', $fileItem->getSourceContent(true));
    }

    public function testSetDestinationContent()
    {
        $fileItem = new FileItem($this->mock, FileItem::TYPE_POST);

        $fileItem->setDestinationContent('<h1>trasformed content</h1>');
        $this->assertEquals('<h1>trasformed content</h1>', $fileItem->getDestinationContent());
    }

    public function testSetDestinationPaths()
    {
        $fileItem = new FileItem($this->mock, FileItem::TYPE_POST);

        $fileItem->setDestinationPaths(array('/spress_test/_site/2013/08/22/example.html'));
        $this->assertTrue(is_array($fileItem->getDestinationPaths()));
        $this->assertCount(1, $fileItem->getDestinationPaths());
    }

    public function testToString()
    {
        $fileItem = new FileItem($this->mock, FileItem::TYPE_POST);

        $this->assertEquals('/spress_test/_posts/world/2013-08-22-example.md', $fileItem);
    }
}
