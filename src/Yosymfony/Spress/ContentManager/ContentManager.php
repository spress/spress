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

use Yosymfony\Spress\Configuration;
use Yosymfony\Spress\ContentLocator\ContentLocator;
use Yosymfony\Spress\Plugin\PluginManager;
use Yosymfony\Spress\Plugin\Event;
 
/**
 * Content manager
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ContentManager
{
    private $renderizer;
    private $converter;
    private $configuration;
    private $contentLocator;
    private $pageItems;
    private $pages;
    private $postItems;
    private $posts;
    private $categories;
    private $tags;
    private $time;
    private $dataResult;
    private $plugin;
    private $events;
    
    /**
     * Constructor
     * 
     * @param Renderizer $renderizer
     * @param Configuration $configuration Configuration manager
     * @param ContentLocator $contentLocator Locate the site content
     * @param array $default Default values
     */
    public function __construct(Renderizer $renderizer, Configuration $configuration, ContentLocator $contentLocator, ConverterManager $converter, PluginManager $plugin)
    {
        $this->configuration = $configuration;
        $this->contentLocator = $contentLocator;
        $this->renderizer = $renderizer;
        $this->converter = $converter;
        $this->plugin = $plugin;
        $this->events = $this->plugin->getDispatcherShortcut();
    }
    
    /**
     * Parse entirely site
     * 
     * @return array Information about process
     */
    public function processSite()
    {
        $this->reset();
        $this->cleanup();
        $this->processExtensible();
        $this->processPages();
        $this->processPost();
        $this->renderPages();
        $this->renderPosts();
        $this->processOthers();
        $this->finish();
        
        return $this->dataResult;
    }
    
    private function reset()
    {   
        $this->pageItems = [];
        $this->pages = [];
        $this->postItems = [];
        $this->posts = [];
        $this->categories = [];
        $this->tags = [];
        $timezone = $this->configuration->getRepository()->get('timezone');
        
        if($timezone)
        {
            date_default_timezone_set($timezone);
        }
        else
        {
            date_default_timezone_set('UTC');
        }
        
        $this->time = new \DateTime('now');
        
        $this->dataResult = [
            'total_post' => 0,
            'processed_post' => 0,
            'drafts_post' => 0,
            'total_pages' => 0,
            'processed_pages' => 0,
            'other_resources' => 0,
        ];
    }
    
    private function processExtensible()
    {
        if(false === $this->configuration->getRepository()->get('safe'))
        {
            $this->plugin->initialize();
            
            $this->events->dispatchStartEvent(
                $this->configuration,
                $this->converter,
                $this->renderizer,
                $this->contentLocator);
        }
        
        $this->converter->initialize();
        $this->contentLocator->setConvertersExtension($this->converter->getExtensions());
    }
    
    private function finish()
    {
        $this->events->dispatchFinish($this->dataResult);
    }
    
    private function cleanup()
    {
        $this->contentLocator->cleanupDestination();
    }
    
    private function processOthers()
    {
        $result = $this->contentLocator->copyRestToDestination();
        $this->dataResult['other_resources'] = count($result);
    }
    
    private function processPages()
    {
        $pageFiles = $this->contentLocator->getPages();
        $this->dataResult['total_pages'] = count($pageFiles);
        
        foreach($pageFiles as $page)
        {
            $pageItem = new PageItem($page, $this->configuration);
            
            $this->events->dispatchBeforeConvertEvent($pageItem);

            if($pageItem->hasFrontmatter())
            {
                $this->converter->convertItem($pageItem);
                
                $this->pages[$pageItem->getId()] = $pageItem->getPayload();
                $this->pageItems[$pageItem->getId()] = $pageItem;
                
                $this->dataResult['processed_pages']++;
                
                $this->events->dispatchAfterConvertEvent($pageItem);
            }
            else
            {
                $this->saveItem($pageItem);
            }
        }
    }
    
    private function processPost()
    { 
        $postFiles = $this->contentLocator->getPosts();
        $this->dataResult['total_post'] = count($postFiles);
        $enableDrafts = $this->configuration->getRepository()->get('drafts');
        
        foreach($postFiles as $post)
        {
            $postItem = new PostItem($post, $this->configuration);
            
            $this->events->dispatchBeforeConvertEvent($postItem, true);
            
            if($postItem->hasFrontmatter() )
            {
                if($postItem->isDraft() && false === $enableDrafts)
                {
                    $this->dataResult['drafts_post']++;
                    continue;
                }
                
                $this->converter->convertItem($postItem);
                
                $payload = $postItem->getPayload();
                $this->posts[$postItem->getId()] = $payload;
                $this->postItems[$postItem->getId()] = $postItem;
                
                foreach($postItem->getCategories() as $category)
                {
                    if(false == isset($this->categories[$category]))
                    {
                        $this->categories[$category] = [];
                    }
                    
                    $this->categories[$category][] = $payload;
                }
                
                foreach($postItem->getTags() as $tag)
                {
                    if(false == isset($this->tags[$tag]))
                    {
                        $this->tags[$tag] = [];
                    }
                    
                    $this->tags[$tag][] = $payload;
                }
                
                $this->dataResult['processed_post']++;
                
                $this->events->dispatchAfterConvertEvent($postItem, true);
            }
        }
        
        $this->events->dispatchAfterConvertPosts(
            array_keys($this->categories),
            array_keys($this->tags));
    }
    
    private function renderPages()
    {
        $payload = $this->getPayload();
        
        foreach($this->pages as $key => $page)
        {   
            $item = $this->pageItems[$key];
            
            $payload['page'] = $page;
         
            $event = $this->events->dispatchBeforeRender($this->renderizer, $payload, $item);
            $rendered = $this->renderizer->renderItem($item, $event->getPayload());
            $this->events->dispatchAfterRender($this->renderizer, $payload, $item);
            
            $this->saveItem($item);
        }
    }
    
    private function renderPosts()
    {
        if(0 == count($this->posts))
        {
            return;    
        }
        
        $fileItemTemplate = null;
        
        $this->sortPost();
        
        $payload = $this->getPayload();
        $paginator = new Paginator($this->posts, $this->configuration->getRepository()->get('paginate'));
        
        if($paginator->getItemsPerPage() > 0)
        {
            $fileItemTemplate = $this->contentLocator->getItem($this->getRelativePathPaginatorTemplate());
        }
        
        foreach($this->posts as $key => $post)
        {
            $item = $this->postItems[$key];
            $payload['page'] = $post;
            
            if($paginator->getItemsPerPage() > 0)
            {
                $payload['paginator'] = $this->getPaginatorPayload($paginator);
                
                
                foreach($payload['paginator']['posts'] as $index => $postPage)
                {
                    $payload['paginator']['posts'][$index]['content'] = $this->renderizer->renderString($postPage['content'], $payload);
                }

                if($fileItemTemplate)
                {
                    $paginatorItemTemplate = new PageItem($fileItemTemplate, $this->configuration);
                    
                    if($paginator->pageChanged() && $paginatorItemTemplate)
                    {
                        $this->renderizer->renderItem($paginatorItemTemplate, $payload);

                        $relativePath = $this->getPageRelativePath($paginator->getCurrentPage());
                        $paginatorItemTemplate->getFileItem()->setDestinationPaths([$relativePath]);
                        $this->saveItem($paginatorItemTemplate);
                    }
                }
                
                $paginator->nextItem();
            }
            
            $event = $this->events->dispatchBeforeRender($this->renderizer, $payload, $item, true);
            $this->renderizer->renderItem($item, $event->getPayload());
            $this->events->dispatchAfterRender($this->renderizer, $payload, $item, true);
            
            $this->saveItem($item);
        }
    }
    
    /**
     * @return array
     */
    private function getPayload()
    {
        $repository = $this->configuration->getRepository();
        
        $result = [];
        $result['spress']  = [];
        $result['spress']['version'] = $this->configuration->getAppVersion();
        $result['spress']['paths'] = $this->configuration->getPaths();
        $result['site'] = $this->configuration->getRepository()->getRaw();
        $result['site']['posts'] = $this->posts;
        $result['site']['pages'] = $this->pages;
        $result['site']['time'] = $this->time;
        $result['site']['categories'] = $this->categories;
        $result['site']['tags'] = $this->tags;
        $result['site']['url'] = $repository->get('url') . $repository->get('baseurl');

        return $result;
    }
    
    private function getPaginatorPayload(Paginator $paginator)
    {        
        $result = [];
        $result['per_page'] = $paginator->getItemsPerPage();
        $result['posts'] = $paginator->getItemsCurrentPage();
        $result['total_posts'] = $paginator->getTotalItems();
        $result['total_pages'] = $paginator->getTotalPages();
        $result['page'] = $paginator->getCurrentPage();
        $result['previous_page'] = $paginator->getPreviousPage();
        $result['previous_page_path'] = $this->getPageRelativePath($result['previous_page']);
        $result['next_page'] = $paginator->getNextPage();
        $result['next_page_path'] = $this->getPageRelativePath($result['next_page']);
        
        return $result;
    }
    
    private function sortPost()
    {
        uasort($this->posts, function($a, $b)
        {
            $dateA = new \Datetime($a['date']);
            $dateB = new \Datetime($b['date']);
            
            if($dateA == $dateB)
            {
                return 0;
            }

            return ($dateA < $dateB) ? 1 : -1;
        });
    }

    /**
     * @return string
     */
    private function getPageRelativeUrl($page)
    {
        $result = false;
        
        if($page)
        {
            $generator = new UrlGenerator();
            $template = $this->configuration->getRepository()->get('paginate_path');
            $result = $generator->getUrl($template, [':num' => $page]);
        }
        
        return $result;
    }
    
    /**
     * @return string
     */
    private function getPageRelativePath($page)
    {
        if($page)
        {
            if(1 == $page)
            {
                return $this->getRelativePathPaginatorTemplate();
            }
            else
            {
                return $this->getPageRelativeUrl($page) . 'index.html';  
            }
        }
        return false;
    }
    
    /**
     * Get relative path of paginator template e.g blog/index.html
     * 
     * @return string
     */
    private function getRelativePathPaginatorTemplate()
    {
        $path = $this->getRelativePathPaginator();
        
        return $path ? $path . '/index.html' : 'index.html';
    }
    
    /**
     * @return string
     */
    private function getRelativePathPaginator()
    {
        $result = '';
        $template = $this->configuration->getRepository()->get('paginate_path');
        $dir = dirname($template);
        
        if($dir != '.')
        {
            $result = ltrim($dir, '/');
        }
        
        return $result;
    }
    
    private function saveItem(ContentItemInterface $contentItem)
    {
        $item = $contentItem->getFileItem();
        $this->contentLocator->saveItem($item);
    }
}