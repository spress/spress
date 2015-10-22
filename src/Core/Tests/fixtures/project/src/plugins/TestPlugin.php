<?php

use Yosymfony\Spress\Core\DataSource\Item;
use Yosymfony\Spress\Core\DataSource\Memory\MemoryDataSource;
use Yosymfony\Spress\Core\Plugin\PluginInterface;
use Yosymfony\Spress\Core\Plugin\EventSubscriber;
use Yosymfony\Spress\Core\Plugin\Event\EnvironmentEvent;
use Yosymfony\Spress\Core\Plugin\Event\ContentEvent;
use Yosymfony\Spress\Core\Plugin\Event\FinishEvent;
use Yosymfony\Spress\Core\Plugin\Event\RenderEvent;

class TestPlugin implements PluginInterface
{
    public function getMetas()
    {
        return [
            'name' => 'Test plugin',
        ];
    }

    public function initialize(EventSubscriber $subscriber)
    {
        $subscriber->addEventListener('spress.start', 'onStart');
        $subscriber->addEventListener('spress.before_convert', 'onBeforeConvert');
        $subscriber->addEventListener('spress.after_convert', 'onAfterConvert');
        $subscriber->addEventListener('spress.before_render_blocks', 'onBeforeRenderBlocks');
        $subscriber->addEventListener('spress.after_render_blocks', 'onAfterRenderBlocks');
        $subscriber->addEventListener('spress.before_render_page', 'onBeforeRenderPage');
        $subscriber->addEventListener('spress.after_render_page', 'onAfterRenderPage');
        $subscriber->addEventListener('spress.finish', 'onFinish');
    }

    public function onStart(EnvironmentEvent $event)
    {
        $dsm = $event->getDataSourceManager();

        $item = new Item('Content from item in memory data source', 'memory-datasource.txt');
        $item->setPath('memory-datasource.txt', Item::SNAPSHOT_PATH_RELATIVE);

        $memoryDataSource = new MemoryDataSource();
        $memoryDataSource->addItem($item);

        $dsm->setDataSource('memory-plugin', $memoryDataSource);
    }

    public function onBeforeConvert(ContentEvent $event)
    {
    }

    public function onAfterConvert(ContentEvent $event)
    {
    }

    public function onBeforeRenderBlocks(RenderEvent $event)
    {
    }

    public function onAfterRenderBlocks(RenderEvent $event)
    {
    }

    public function onBeforeRenderPage(RenderEvent $event)
    {
    }

    public function onAfterRenderPage(RenderEvent $event)
    {
    }

    public function onFinish(FinishEvent $event)
    {
    }
}
