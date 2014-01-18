<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Tests;

use Symfony\Component\Finder\SplFileInfo;

use Yosymfony\Spress\Application;
use Yosymfony\Spress\ContentLocator\FileItem;
use Yosymfony\Spress\ContentManager\ConverterManager;
use Yosymfony\Spress\ContentManager\PageItem;

class ConverterManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $cm;
    protected $pagesDir;
    protected $configuration;
    
    public function setUp()
    {
        $this->pagesDir = realpath(__DIR__ .'/../fixtures/project/');
        
        $app = new Application();
        $this->configuration = $app['spress.config'];
        $this->cm = $app['spress.cms.converter'];
        $this->cm->initialize();
    }
    
    public function testConverterManager()
    {
        $path = $this->pagesDir . '/projects/index.md';
        $fileInfo = new SplFileInfo($path, 'projects', 'projects/index.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_PAGE);
        $item = new PageItem($fileItem, $this->configuration);
        $cr = $this->cm->convertItem($item);

        $this->assertInstanceOf('Yosymfony\\Spress\\ContentManager\\ConverterResult', $cr);
        $this->assertEquals('html', $cr->getExtension());
        $this->assertTrue(strlen($cr->getResult()) > 0);
    }
}