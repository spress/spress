<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\Plugin;

use Yosymfony\Spress\Plugin\Event;
use Yosymfony\Spress\Core\Configuration;
use Yosymfony\Spress\Core\ContentManager\ConverterManager;
use Yosymfony\Spress\Core\ContentManager\ContentItemInterface;
use Yosymfony\Spress\Core\ContentLocator\ContentLocator;
use Yosymfony\Spress\Core\ContentManager\Renderizer;
use Yosymfony\Spress\Core\IO\IOInterface;

/**
 * Shortcut for dispatch events
 */
class DispatcherShortcut
{
    private $pm;
    
    /**
     * Constructor
     * 
     * @param Yosymfony\Spress\Core\Plugin\PluginManager $pluginManager
     */
    public function __construct(PluginManager $pluginManager)
    {
        $this->pm = $pluginManager;
    }
    
    public function dispatchStartEvent(
        Configuration $configuration, 
        ConverterManager $converter, 
        Renderizer $renderizer, 
        ContentLocator $contentLocator,
        IOInterface $io)
    {
        $event = new Event\EnvironmentEvent(
            $configuration,
            $converter,
            $renderizer,
            $contentLocator,
            $io);
        
        return $this->pm->dispatchEvent(Event\SpressEvents::SPRESS_START, $event);
    }
    
    public function dispatchBeforeConvertEvent(ContentItemInterface $item, $isPost = false)
    {
        $event = new Event\ConvertEvent($item, $isPost);
        
        return $this->pm->dispatchEvent(Event\SpressEvents::SPRESS_BEFORE_CONVERT, $event);
    }
    
    public function dispatchAfterConvertEvent(ContentItemInterface $item, $isPost = false)
    {
        $event = new Event\ConvertEvent($item, $isPost);
        
        return $this->pm->dispatchEvent(Event\SpressEvents::SPRESS_AFTER_CONVERT, $event);
    }
    
    public function dispatchAfterConvertPosts(array $categories, array $tags)
    {
        $event = new Event\AfterConvertPostsEvent($categories, $tags);
        
        return $this->pm->dispatchEvent(Event\SpressEvents::SPRESS_AFTER_CONVERT_POSTS, $event);
    }
    
    public function dispatchBeforeRender(Renderizer $render, array $payload, ContentItemInterface $item, $isPost = false)
    {
        $event = new Event\RenderEvent($render, $payload, $item, $isPost);
        
        return $this->pm->dispatchEvent(Event\SpressEvents::SPRESS_BEFORE_RENDER, $event);
    }
    
    public function dispatchAfterRender(Renderizer $render, array $payload, ContentItemInterface $item, $isPost = false)
    {
        $event = new Event\RenderEvent($render, $payload, $item, $isPost);
        
        return $this->pm->dispatchEvent(Event\SpressEvents::SPRESS_AFTER_RENDER, $event);
    }
    
    public function dispatchBeforeRenderPagination(Renderizer $render, array $payload, ContentItemInterface $item)
    {
        $event = new Event\RenderEvent($render, $payload, $item, true);
        
        return $this->pm->dispatchEvent(Event\SpressEvents::SPRESS_BEFORE_RENDER_PAGINATION, $event);
    }
    
    public function dispatchAfterRenderPagination(Renderizer $render, array $payload, ContentItemInterface $item)
    {
        $event = new Event\RenderEvent($render, $payload, $item, true);
        
        return $this->pm->dispatchEvent(Event\SpressEvents::SPRESS_AFTER_RENDER_PAGINATION, $event);
    }
    
    public function dispatchFinish(array $result)
    {
        $event = new Event\FinishEvent($result);
        
        return $this->pm->dispatchEvent(Event\SpressEvents::SPRESS_FINISH, $event);
    }
}