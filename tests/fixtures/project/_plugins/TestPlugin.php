<?php

use Symfony\Component\EventDispatcher\Event;
use Yosymfony\Spress\Plugin\Plugin;
use Yosymfony\Spress\Plugin\EventSubscriber;
use Yosymfony\Spress\Plugin\Event\EnviromentEvent;

class TestPlugin extends Plugin
{
    public function getMetas()
    {
        return [
            'name' => 'TestPlugin',
            'author' => 'Yo! Symfony',
        ];
    }
    
    public function initialize(EventSubscriber $subscriber)
    {
       $subscriber->addEventListener('spress.start', 'onStart');
    }
    
    public function onStart(EnviromentEvent $event)
    {   
        $event->getConfigRepository()->set('onStartEvent', 'test value');
    }
}