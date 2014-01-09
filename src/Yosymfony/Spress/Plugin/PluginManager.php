<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Plugin;

use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\SplFileInfo;
use Composer\Autoload\ClassLoader;
use Yosymfony\Spress\ContentLocator\ContentLocator;

/**
 * Plugin manager
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PluginManager
{
    const VENDORS_DIR = "vendors";
    
    private $contentLocator;
    private $loader;
    private $dispatcher;
    private $plugins = [];
    private $eventsDispatched = [];
    private $dispatcherShortcut;
    
    /**
     * Constructor
     * 
     * @param ContentLocator $contentLocator
     * @param $classLoader
     */
    public function __construct(ContentLocator $contentLocator, ClassLoader $classLoader)
    {
        $this->contentLocator = $contentLocator;
        $this->loader = $classLoader;
        $this->dispatcher = new EventDispatcher();
        $this->dispatcherShortcut = new DispatcherShortcut($this);
    }
    
    /**
     * Start the life clycle
     */
    public function initialize()
    {
        $this->eventsDispatched = [];
        
        $this->updateClassLoader();
        
        $this->plugins = $this->getPlugins();
        
        $this->processPlugins();
    }
    
    /**
     * Dispatch a event
     * 
     * @param string $eventName Name of event
     * @param Event $event Event object
     * 
     * @return Symfony\Component\EventDispatcher\Event
     */
    public function dispatchEvent($eventName, Event $event = null)
    {
        $this->eventsDispatched[] = $eventName;
        
        return $this->dispatcher->dispatch($eventName, $event);
    }
    
    /**
     * Get plugins available
     * 
     * @return array Array of PluginItem
     */
    public function getPlugins()
    {
        $result = [];
        $dir = $this->contentLocator->getPluginDir();
        
        if($dir)
        {
            $finder = new Finder();
            $finder->files()
                ->in($dir)
                ->exclude(self::VENDORS_DIR)
                ->name('composer.json');
                
            foreach($finder as $file)
            {
                $pluginConf = $this->getPluginConfigData($file);
                
                if($pluginConf->isValidPlugin())
                {
                    $className = $pluginConf->getSpressClass();
       
                    if($this->isValidClassName($className))
                    {
                        $result[] = new PluginItem(new $className);
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Get the events name dispatched
     * 
     * @return array
     */
    public function getHistoryEventsDispatched()
    {
        return $this->eventsDispatched;
    }
    
    /**
     * Get the distpatcher shortcut
     * 
     * @return DispatcherShortcut
     */
    public function getDispatcherShortcut()
    {
        return $this->dispatcherShortcut;
    }
    
    private function updateClassLoader()
    {
        $baseDir = $this->contentLocator->getPluginDir();
        $vendorDir = $baseDir . '/' . self::VENDORS_DIR;
        $vendorComposer = $vendorDir . '/composer';
        
        if(false == file_exists($vendorDir))
        {
            return;
        }

        $map = require $vendorComposer . '/autoload_namespaces.php';
        foreach ($map as $namespace => $path) {
            $this->loader->set($namespace, $path);
        }

        $map = require $vendorComposer . '/autoload_psr4.php';
        foreach ($map as $namespace => $path) {
            $this->loader->setPsr4($namespace, $path);
        }

        $classMap = require $vendorComposer . '/autoload_classmap.php';
        if ($classMap) {
            $this->loader->addClassMap($classMap);
        }
    }
    
    private function processPlugins()
    {
        foreach($this->plugins as $pluginItem)
        {
            $subscriber = new EventSubscriber();
            $pluginItem->getPlugin()->initialize($subscriber);
            $this->addListeners($pluginItem, $subscriber);
        }
    }
    
    private function addListeners(PluginItem $pluginItem, EventSubscriber $subscriber)
    {
        foreach($subscriber->getEventListeners() as $eventName => $listener)
        {
            if(true == is_string($listener))
            {
                $this->dispatcher->addListener($eventName, [$pluginItem->getPlugin(), $listener]);
            }
            else
            {
                $this->dispatcher->addListener($eventName, $listener);
            }
        }
    }
    
    private function isValidClassName($name)
    {
        $result = false;

        if(class_exists($name))
        {
            $implements = class_implements($name);
            
            if(isset($implements['Yosymfony\Spress\Plugin\PluginInterface']))
            {
                $result = true;
            }
        }
        
        return $result;
    }
    
    private function getPluginConfigData(SplFileInfo $item)
    {
        $json = $item->getContents();
        $data = json_decode($json, true);
        
        return new PluginConfigData($data);
    }
}