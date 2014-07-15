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

class ConvertEventTest extends \PHPUnit_Framework_TestCase
{
    protected $item;
    
    public function setUp()
    {
        $path = realpath(__DIR__ .'/../../../fixtures/project/_posts/2013-08-12-post-example-1.md');
        
        $app = new Application();
        $config = $app['spress.config'];
        
        $fileInfo = new SplFileInfo($path, '', '2013-08-12-post-example-1.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);
        
        $this->item = new PostItem($fileItem, $config);
        $this->item->setPostConverterContent($this->item->getPreConverterContent());
        $this->item->setOutExtension('html');
    }
    
    public function testGetFrontmatter()
    {
        $event = new ConvertEvent($this->item);
        $fm = $event->getFrontmatter();
        
        $this->assertTrue(is_array($fm));
        $this->assertGreaterThan(0, $fm);
    }
    
    public function testSetFrontmatter()
    {
        $event = new ConvertEvent($this->item);
        $event->setFrontmatter(['key1' => 'value1']);
        
        $this->assertTrue($this->item->getFrontmatter()->hasFrontmatter());
        $this->assertEquals('value1', $this->item->getFrontmatter()->getFrontmatter()->get('key1'));
    }
}