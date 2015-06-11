<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\tests\Plugin\Event;

use Symfony\Component\Finder\SplFileInfo;
use Yosymfony\Spress\Core\Application;
use Yosymfony\Spress\Core\ContentLocator\FileItem;
use Yosymfony\Spress\Core\ContentManager\PostItem;
use Yosymfony\Spress\Core\Plugin\Event\RenderEvent;

class RenderEventTest extends \PHPUnit_Framework_TestCase
{
    protected $item;
    protected $renderizer;

    public function setUp()
    {
        $path = realpath(__DIR__.'/../../fixtures/project/_posts/2013-08-12-post-example-1.md');

        $app = new Application();
        $config = $app['spress.config'];
        $config->loadLocal(__DIR__.'/../../fixtures/project');

        $fileInfo = new SplFileInfo($path, '', '2013-08-12-post-example-1.md');
        $fileItem = new FileItem($fileInfo, FileItem::TYPE_POST);

        $this->item = new PostItem($fileItem, $config);
        $this->item->setPostConverterContent($this->item->getPreConverterContent());
        $this->item->setOutExtension('html');

        $this->renderizer = $app['spress.cms.renderizer'];
    }

    public function testRender()
    {
        $event = new RenderEvent($this->renderizer, [], $this->item);
        $rendered = $event->render('{{ name }}', ['name' => 'Spress']);

        $this->assertEquals('Spress', $rendered);
    }

    public function testGetPayload()
    {
        $payload = ['name' => 'Spress'];
        $event = new RenderEvent($this->renderizer, $payload, $this->item);

        $this->assertTrue(is_array($event->getPayload()));
        $this->assertCount(1, $event->getPayload());
        $this->assertArrayHasKey('name', $event->getPayload());
    }

    public function testSetPayload()
    {
        $event = new RenderEvent($this->renderizer, [], $this->item);
        $event->setPayload(['name' => 'Spress']);

        $this->assertTrue(is_array($event->getPayload()));
        $this->assertCount(1, $event->getPayload());
        $this->assertArrayHasKey('name', $event->getPayload());
    }
}
