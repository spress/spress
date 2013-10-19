<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\ContentManager;

use Yosymfony\Spress\TwigFactory;
use Yosymfony\Spress\Configuration;
use Yosymfony\Spress\ContentLocator\ContentLocator;
use Yosymfony\Spress\Exception\FrontmatterValueException;

/**
 * Content renderizer
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Renderizer
{
    private $twig;
    private $contentLocator;
    private $configuration;
    private $layoutNamespace = 'layout';
    
    private $layoutItems;
    
    /**
     * Constructor
     * 
     * @param TwigFactory $twigFactory
     * @param ContentLocator $contentLocator
     * @param Configuration $config
     */
    public function __construct(TwigFactory $twigFactory, ContentLocator $contentLocator, Configuration $configuration)
    {
        $this->contentLocator = $contentLocator;
        $this->configuration = $configuration;
        $this->layoutItems = $this->contentLocator->getLayouts();
        
        $this->buildTwig($twigFactory, $this->layoutItems);
    }
    
    /**
     * Render a content items
     * 
     * @param ContentItemInterface $item
     * @param array $payload
     * 
     * @return string
     */
    public function renderItem(ContentItemInterface $item, array $payload = [])
    {
        $content = $item->getDestinationContent();
        $rendered = $this->renderString($content, $payload);
        
        $layoutName = $this->getItemLayoutName($item);
        
        if($layoutName)
        {
            $payload['content'] = $rendered;

            return $this->renderString($this->getTwigEntryPoint($layoutName), $payload);
        }

        return $rendered;
    }
    
    /**
     * Render string value
     * 
     * @param string $value
     * @param array payload
     * 
     * @return string
     */
    public function renderString($value, array $payload = [])
    {
        return $this->twig->render($value, $payload);
    }
    
    /**
     * Exists layout? e.g default
     * 
     * @param string $name
     * 
     * @return @bool
     */
    public function existsLayout($name)
    {
        return isset($this->layoutItems[$name . '.html']);
    }
    
    /**
     * @return string
     */
    private function getTwigEntryPoint($layoutName)
    {
        $result = '';
        $layout = $this->getLayoutName($layoutName . '.html');
        
        if(strlen($layoutName) > 0)
        {
            $result = "{% extends \"$layout\" %}";
        }
        
        return $result;
    }
    
    /**
     * @return string
     */
    private function getLayoutName($name)
    {
        return sprintf('@%s/%s', $this->layoutNamespace, $name);
    }
    
    /**
     * @return string
     */
    private function getItemLayoutName($item)
    {
        $layoutName = $item->getFrontmatter()->get('layout');
        
        if($layoutName)
        {
            if(false == is_string($layoutName))
            {
                throw new FrontmatterValueException(
                    sprintf('Invalid value.', $layoutName), 
                    'layout', 
                    $item->getFileItem()->getFileName()
                );
            }
            
            if(false == $this->existsLayout($layoutName))
            {
                throw new FrontmatterValueException(
                    sprintf('Layout "%s" not found.', $layoutName), 
                    'layout', 
                    $item->getFileItem()->getFileName()
                );
            }
            
            return $layoutName;
        }
        else
        {
            return '';
        }
    }
    
    private function processLayouts(array $layouts)
    {
        $result = [];
        
        foreach($layouts as $layout)
        {
            $pageItem = new PageItem($layout, $this->configuration);
            
            $layoutName = $this->getItemLayoutName($pageItem);
            $content = $pageItem->getContent();
            
            if($layoutName)
            {
                $content = $this->getTwigEntryPoint($layoutName) . $content;
            }
            
            $name = $this->getLayoutName($layout->getRelativePathFilename());
            $result[$name] = $content;
        }
        
        return $result;
    }
    
    private function buildTwig($twigFactory, array $layouts)
    {
        $templates = $this->processLayouts($layouts);
        $includesDir = $this->contentLocator->getIncludesDir();
        $extraDirs = [];
        
        if($includesDir)
        {
            $extraDirs[] = $includesDir;
        }
        
        $this->twig = $twigFactory
            ->withAutoescape(false)
            ->withCache(false)
            ->addLoaderFilesystem($extraDirs)
            ->addLoaderArray($templates)
            ->addLoaderString()
            ->create();
    }
}