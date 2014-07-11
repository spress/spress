<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Tests\Plugin\Event;

use Symfony\Component\Finder\SplFileInfo;
use Yosymfony\Spress\Core\Application;
use Yosymfony\Spress\Core\ContentLocator\FileItem;
use Yosymfony\Spress\Core\ContentManager\PostItem;
use Yosymfony\Spress\Plugin\Event\ConvertEvent;

class ContentEventTest extends \PHPUnit_Framework_TestCase
{
    protected $item;
    
    public function setUp()
    {
        $path = realpath(__DIR__ .'/../../fixtures/project/_posts/2013-08-12-post-example-1.md');
        
        $app = new Application();
        $config = $app['spress.config'];
        
        $fileInfo = new SplFileInfo($path, '', '2013-08-12-post-example-1.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        
        $this->item = new PostItem($fileItem, $config);
        $this->item->setPostConverterContent($this->item->getPreConverterContent());
        $this->item->setOutExtension('html');
    }
    
    public function testIsPost()
    {
        $event = new ConvertEvent($this->item, true);
        
        $this->assertTrue($event->isPost());
    }
    
    public function testIsNotPost()
    {
        $event = new ConvertEvent($this->item);
        
        $this->assertFalse($event->isPost());
    }
    
    public function testGetContent()
    {
        $event = new ConvertEvent($this->item);
        
        $this->assertEquals($this->item->getPreConverterContent(), $event->getContent());
    }
    
    public function testSetContent()
    {
        $event = new ConvertEvent($this->item);
        $event->setContent('New content');
        
        $this->assertEquals('New content', $this->item->getPreConverterContent());
    }
    
    public function testGetRelativePath()
    {
        $event = new ConvertEvent($this->item);
        
        $this->assertEquals('2013-08-12-post-example-1.md', $event->getRelativePath());
    }
}