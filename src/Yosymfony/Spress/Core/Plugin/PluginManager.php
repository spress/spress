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

use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\SplFileInfo;
use Composer\Autoload\ClassLoader;
use Dflydev\EmbeddedComposer\Core\EmbeddedComposerBuilder;
use Yosymfony\Spress\Core\ContentLocator\ContentLocator;
use Yosymfony\Spress\Plugin\EventSubscriber;

/**
 * Plugins manager
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PluginManager
{
    private $options = [];
    private $contentLocator;
    private $classLoader;
    private $dispatcher;
    private $plugins = [];
    private $eventsDispatched = [];
    private $dispatcherShortcut;
    
    /**
     * Constructor
     * 
     * @param ContentLocator $contentLocator
     * @param ClassLoader $classLoader
     * @param array $options
     */
    public function __construct(ContentLocator $contentLocator, ClassLoader $classLoader, array $options)
    {
        $this->options = $options;
        $this->contentLocator = $contentLocator;
        $this->classLoader = $classLoader;
        $this->dispatcher = new EventDispatcher();
        $this->dispatcherShortcut = new DispatcherShortcut($this);
    }
    
    /**
     * Start the life clycle
     * 
     * @expectedException \RuntimeException If not there mandatory options
     */
    public function initialize()
    {
        $this->checkOptions($this->options);
        
        $this->eventsDispatched = [];
        
        $this->updateClassLoader();
        
        $this->dispatcher = new EventDispatcher();
        
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
     * Get plugins availables
     * 
     * @return array Array of PluginItem
     */
    public function getPlugins()
    {
        $plugins = [];
        $composerPlugins = [];
        $dir = $this->contentLocator->getPluginDir();
        
        if($dir)
        {
            $plugins = $this->getNotNamespacePlugins($dir);
            $composerPlugins = $this->getComposerPlugins($dir);
        }
        
        return array_merge($plugins, $composerPlugins);
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
    
    private function getNotNamespacePlugins($dir)
    {
        $plugins = [];
        
        $finder = new Finder();
        $finder->files()
            ->in($dir)
            ->exclude($this->options['vendors_dir'])
            ->name('*.php');
            
        foreach($finder as $file)
        {
            $className = $file->getBasename('.php');
            include_once($file->getRealPath());
            
            if($this->isValidClassName($className))
            {
                $plugins[$className] = new PluginItem(new $className);
            }
        }
        
        return $plugins;
    }
    
    private function getComposerPlugins($dir)
    {
        $plugins = [];
        
        $finder = new Finder();
        $finder->files()
            ->in($dir)
            ->exclude($this->options['vendors_dir'])
            ->name($this->options['composer_filename']);
            
        foreach($finder as $file)
        {
            $pluginConf = $this->getPluginComposerData($file);

            $className = $pluginConf->getSpressClass();
            
            if($className)
            {
                if($this->isValidClassName($className))
                {
                    $plugins[$className] = new PluginItem(new $className);
                }
            }
        }
        
        return $plugins;
    }
    
    private function updateClassLoader()
    {
        $baseDir = $this->contentLocator->getPluginDir();
        $vendorDir = $baseDir . '/' . $this->options['vendors_dir'];
        
        if(false == file_exists($vendorDir))
        {
            return;
        }
        
        $embeddedComposerBuilder = new EmbeddedComposerBuilder(
            $this->classLoader,
            $this->contentLocator->getSourceDir()
        );
        
        $embeddedComposer = $embeddedComposerBuilder
            ->setComposerFilename($this->options['composer_filename'])
            ->setVendorDirectory($vendorDir)
            ->build();
        
        $embeddedComposer->processAdditionalAutoloads();
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
            
            if(isset($implements['Yosymfony\\Spress\\Core\\Plugin\\PluginInterface']))
            {
                $result = true;
            }
        }
        
        return $result;
    }
    
    private function getPluginComposerData(SplFileInfo $item)
    {
        $json = $item->getContents();
        $data = json_decode($json, true);
        
        return new PluginComposerData($data);
    }
    
    private function checkOptions(array $options)
    {
        if(false == isset($options['vendors_dir']))
        {
            throw new \RuntimeException('vendors_dir option is necessary for Plugin Manager');
        }
        
        if(false == isset($options['composer_filename']))
        {
            throw new \RuntimeException('composer_filename option is necessary for Plugin Manager');
        }
    }
}