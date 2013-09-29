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

use Yosymfony\Spress\Exception\FrontmatterValueException;
 
/**
 * Content manager
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ContentManager
{
    private $renderizer;
    private $markdown;
    private $markdownExt;
    private $configuration;
    private $contentLocator;
    private $pageItems;
    private $pages;
    private $postItems;
    private $posts;
    private $categories;
    private $tags;
    private $time;
    private $paginationTotalPost;
    private $paginationTotal;
    private $paginationPerPage;
    private $paginationTo;
    private $layoutNamespace = 'layout';
    private $dataResult;
    
    /**
     * Constructor
     * 
     * @param MarkdownWrapper $markdown
     * @param Configuration $configuration Configuration manager
     * @param ContentLocator $contentLocator Locate the site content
     */
    public function __construct($renderizer, $markdown, $configuration, $contentLocator)
    {
        $this->markdown = $markdown;
        $this->configuration = $configuration;
        $this->contentLocator = $contentLocator;
        $this->markdownExt = $configuration->getRepository()->get('markdown_ext');
        $this->renderizer = $renderizer;
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
        $this->processPages();
        $this->processPost();
        $this->renderPages();
        $this->renderPosts();
        $this->processOthers();
        
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

            if($pageItem->hasFrontmatter())
            {
                if($this->isExtensionMarkdown($pageItem))
                {
                    $content = $pageItem->getContent();
                    $contentMd = $this->markdown->parse($content);
                    $pageItem->setDestinationContent($contentMd);
                }
                
                $this->pages[$pageItem->getId()] = $pageItem->getPayload();
                $this->pageItems[$pageItem->getId()] = $pageItem;
                
                $this->dataResult['processed_pages']++;
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
            
            if($postItem->hasFrontmatter() )
            {
                if($postItem->isDraft() && false === $enableDrafts)
                {
                    $this->dataResult['drafts_post']++;
                    continue;
                }
                
                $content = $postItem->getContent();
                $contentMd = $this->markdown->parse($content);
                $postItem->setDestinationContent($contentMd, true);
                
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
            }
        }
    }
    
    private function renderPages()
    {
        $payload = $this->getPayload();
        
        foreach($this->pages as $key => $page)
        {
            $item = $this->pageItems[$key];
            
            $payload['page'] = $page;
            
            $rendered = $this->renderizer->renderItem($item, $payload);
            $item->setDestinationContent($rendered);
            $this->saveItem($item);
        }
    }
    
    private function renderPosts()
    {
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
                        $renderedPaginator = $this->renderizer->renderItem($paginatorItemTemplate, $payload);
                        $paginatorItemTemplate->setDestinationContent($renderedPaginator);
                        
                        $relativePath = $this->getPageRelativePath($paginator->getCurrentPage());
                        $paginatorItemTemplate->getFileItem()->setDestinationPaths([$relativePath]);
                        $this->saveItem($paginatorItemTemplate);
                    }
                }
                
                $paginator->nextItem();
            }
            
            $rendered = $this->renderizer->renderItem($item, $payload);
            $item->setDestinationContent($rendered);
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
            if($a['date'] == $b['date'])
            {
                return 0;
            }
            
            return ($a['date'] < $b['date']) ? -1 : 1;
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
    
    private function isExtensionMarkdown(ContentItemInterface $contentItem)
    {
        return in_array($contentItem->getFileItem()->getExtension(), $this->markdownExt);
    }
    
    private function saveItem(ContentItemInterface $contentItem)
    {
        $item = $contentItem->getFileItem();
        $this->contentLocator->saveItem($item);
    }
}