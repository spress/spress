<?php

use Yosymfony\Spress\Plugin\Event;
use Yosymfony\Spress\Plugin\Plugin;
use Yosymfony\Spress\Plugin\EventSubscriber;

class TestPlugin extends Plugin
{
    public function initialize(EventSubscriber $subscriber)
    {
        $subscriber->addEventListener('spress.start', 'onStart');
        $subscriber->addEventListener('spress.before_convert', 'onBefore_convert');
        $subscriber->addEventListener('spress.after_convert', 'onAfter_convert');
        $subscriber->addEventListener('spress.after_convert_posts', 'onAfter_convert_posts');
        $subscriber->addEventListener('spress.before_render', 'onBefore_render');
        $subscriber->addEventListener('spress.after_render', 'onAfter_render');
        $subscriber->addEventListener('spress.before_render_pagination', 'onBefore_render_pagination');
        $subscriber->addEventListener('spress.after_render_pagination', 'onAfter_render_pagination');
        $subscriber->addEventListener('spress.finish', 'onFinish');
    }

    public function onStart(Event\EnvironmentEvent $event)
    {
    }

    public function onBefore_convert(Event\ConvertEvent $event)
    {
    }

    public function onAfter_convert(Event\ConvertEvent $event)
    {
    }

    public function onAfter_convert_posts(Event\AfterConvertPostsEvent $event)
    {
    }

    public function onBefore_render(Event\RenderEvent $event)
    {
    }

    public function onAfter_render(Event\RenderEvent $event)
    {
    }

    public function onBefore_render_pagination(Event\RenderEvent $event)
    {
    }

    public function onAfter_render_pagination(Event\RenderEvent $event)
    {
    }

    public function onFinish(Event\FinishEvent $event)
    {
    }
}
