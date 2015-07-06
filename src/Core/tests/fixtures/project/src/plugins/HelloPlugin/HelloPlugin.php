<?php

namespace MyVendor\HelloPlugin;

use Yosymfony\Spress\Core\Plugin\Event;
use Yosymfony\Spress\Core\Plugin\PluginInterface;
use Yosymfony\Spress\Core\Plugin\EventSubscriber;

class HelloPlugin implements PluginInterface
{
    public function getMetas()
    {
        return [
            'name' => 'Hello plugin',
        ];
    }

    public function initialize(EventSubscriber $subscriber)
    {
        $subscriber->addEventListener('spress.start', 'onStart');
    }

    public function onStart(Event\EnvironmentEvent $event)
    {
    }
}
