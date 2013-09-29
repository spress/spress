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

use Yosymfony\Spress\Utils;
use Yosymfony\Spress\ContentLocator\FileItem;
use Yosymfony\Spress\Exception\FrontmatterValueException;

class PostItem implements ContentItemInterface
{
    private $fileItem;
    private $content;
    private $contentNotMarkdown;
    private $configuration;
    private $frontmatter;
    private $title;
    private $date;
    
    /**
     * Constructor
     * 
     * @param FileItem $fileItem
     * @param Yosymfony\Spress\Configuration $configuration
     */
    public function __construct(FileItem $fileItem, $configuration)
    {
        if($fileItem->getType() !== FileItem::TYPE_POST)
        {
            throw new \InvalidArgumentException(sprintf('Type item "%s" is invalid in post item'));
        }
        
        $this->fileItem = $fileItem;
        $this->configuration = $configuration;
        $this->frontmatter = new Frontmatter($this->fileItem->getSourceContent(), $configuration);
        $this->content = $this->frontmatter->getContentNotFrontmatter();
        $this->extractDataFromFilename();
        $this->setUpDestinationPath();
    }
    
    /**
     * Get content without metadate like Front-matter
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Get item identifier
     * 
     * @return string
     */
    public function getId()
    {
        $parts = $this->fileItem->getRelativePathExplode();
        $parts[] = $this->fileItem->getFileName(false);
        $parts[] = $this->fileItem->getExtension();
        
        return implode('-', $parts);
    }
    
    /**
     * Get the relative URL. e.g. /about/me.html
     * 
     * @return string
     */
    public function getUrl()
    {
        $repository = $this->configuration->getRepository();
        
        if(true === $repository->get('relative_permalinks'))
        {
            return $this->getRelativeURL();
        }
        else
        {
            $generator = new UrlGenerator();
            
            return $generator->getUrl(':url_base/:path', [
                ':url_base' => $repository->get('url') . $repository->get('baseurl'),
                ':path' => $this->getRelativeURL(),
            ]);
        }
    }
    
    /**
     * Has Front-matter?
     * 
     * @return bool
     */
    public function hasFrontmatter()
    {
        return $this->frontmatter->hasFrontmatter();
    }
    
    /**
     * Get Front-matter
     * 
     * @return Yosymfony\Silex\ConfigServiceProvider\ConfigRepository
     */
    public function getFrontmatter()
    {   
        return $this->frontmatter->getFrontmatter();
    }
    
    /**
     * Set the destination (transformed) content.
     * 
     * @param string $content
     */
    public function setDestinationContent($content, $fromMarkdown = false)
    {
        if($fromMarkdown)
        {
            $this->contentNotMarkdown = $content;
        }
        
        $this->fileItem->setDestinationContent($content);
    }
    
    /**
     * Get the destination (transformed) content.
     * 
     * return string
     */
    public function getDestinationContent()
    {
        return $this->fileItem->getDestinationContent();
    }
    
    /**
     * Get item payload
     * 
     * @return array
     */
    public function getPayload()
    {
        $fm = $this->getFrontmatter();
        
        $repository = $this->configuration->createBlankRepository();
        $repository->set('title', $this->getTitle());
        $repository->set('date', $this->getDate());
        $repository->set('categories', $this->getCategories());
        $repository->set('tags', $this->getTags());
        $repository->set('url', $this->getUrl());
        $repository->set('content', $this->contentNotMarkdown);
        $repository->set('id', $this->getId());
        $repository->set('path', $this->getRelativePath());
        
        return $repository->mergeWith($fm)->getRaw();
    }
    
    /**
     * Get date
     * 
     * @return DateTime
     */
    public function getDate()
    {
        $result = $this->date;
        $dateStr = $this->getFrontmatter()->get('date');
        
        if($dateStr)
        {
            try
            {
                $result = new \DateTime($dateStr); 
            }
            catch (\Exception $e)
            {
                throw new FrontmatterValueException('Invalid value. Expected date string', 'date', $this->fileItem->getFilename());
            }
        }
        
        return $result;
    }
    
    /**
     * Get Title
     * 
     * @return string
     */
    public function getTitle()
    {
        $title = $this->getFrontmatter()->get('title');
        
        return $title ?: $this->title;
    }
    
    /**
     * Get categories
     * 
     * @return array
     */
    public function getCategories()
    {
        $categories = $this->getFrontmatter()->get('categories', []);
        $categoriesFromPath = $this->fileItem->getRelativePathExplode();
        
        if(false == is_array($categories))
        {
            throw new FrontmatterValueException('Invalid value. Expected array.', 'categories', $this->fileItem->getFilename());
        }
        
        $result = count($categories) > 0 ? $categories : $categoriesFromPath;
        
        return array_unique($result);
    }
    
    /**
     * get tags
     * 
     * @return array
     */
    public function getTags()
    {
        $tags = $this->getFrontmatter()->get('tags', []);
        
        if(false == is_array($tags))
        {
            throw new FrontmatterValueException('Invalid value. Expected array.', 'tags', $this->fileItem->getFilename());
        }
        
        return array_unique($tags);
    }
    
    /**
     * Is draft?
     * 
     * @return bool
     */
    public function isDraft()
    {
        $draft = $this->getFrontmatter()->get('draft', false);
        
        if(false == is_bool($draft))
        {
            throw new FrontmatterValueException('Invalid value. Expected boolean.', 'draft', $this->fileItem->getFilename());
        }
        
        return $draft;
    }
    
    public function getFileItem()
    {
        return $this->fileItem;
    }
    
    /**
     * @return string
     */
    private function getRelativeURL()
    {
        $generator = new UrlGenerator();
        $url = $generator->getUrl($this->getUrlTemplate(), $this->getUrlPlaceholders());
        
        return $url;
    }
    
    /**
     * @return string
     */
    private function getUrlTemplate()
    {
        $template = $this->configuration->getRepository()->get('permalink');
        
        switch($template)
        {
            case 'pretty':
                $template = '/:categories/:year/:month/:day/:title/';
                break;
            case 'ordinal':
                $template = '/:categories/:year/:i_day/:title.html';
                break;
            case 'date':
                $template = '/:year/:month/:day/:title.html';
                break;
        }
        
        return $template;
    }
    
    /**
     * @return array
     */
    private function getUrlPlaceholders()
    {
        $time = $this->getDate();
        
        return [
            ':categories'   => $this->getCategoriesPath(),
            ':title'        => $this->getTitleSlugified(),
            ':year'         => $time->format('Y'),
            ':month'        => $time->format('m'),
            ':day'          => $time->format('d'),
            ':i_month'      => $time->format('n'),
            ':i_day'        => $time->format('j'),
        ];
    }
    
    /**
     * @return string
     */
    private function getCategoriesPath()
    {
        return implode('/', array_map(function($a){
            return Utils::slugify($a);
        }, $this->getCategories()));
    }
    
    private function getTitleSlugified()
    {
        return Utils::slugify($this->getTitle());
    }
    
    private function extractDataFromFilename()
    {
        $parts = explode('-', $this->fileItem->getFilename(false), 4);
        
        if(count($parts) < 4)
        {
            throw new \InvalidArgumentException(sprintf('Invalid post filename: "%s". Expected year-moth-day-title.', $this->fileItem->getFilename()));
        }
        
        $this->title = implode(' ', explode('-', $parts[3]));
        
        if(0 == strlen($this->title))
        {
            throw new \InvalidArgumentException(sprintf('Invalid post filename: "%s". Expected year-moth-day-title.', $this->fileItem->getFilename()));
        }
        
        $this->date = new \DateTime();
        
        try
        {
            $this->date->setDate($parts[0], $parts[1], $parts[2]);
        }
        catch(Exception $e)
        {
            throw new \InvalidArgumentException(sprintf('Invalid post filename: "%s". Does not have a valid date.', $this->fileItem->getFilename()));
        }
    }
    
    /**
     * @return string
     */
    private function getRelativePath()
    {
        $path = rtrim($this->getRelativeURL(), '/');
        
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if(0 == strlen($extension))
        {
            if($this->hasFrontmatter())
            {
                $path .= '/index.html';
            }
            else
            {
                $path .= '/' .$this->fileItem->getFilename();
            }
        }
        
        return $path;
    }
    
    private function setUpDestinationPath()
    {
        $destination = $this->getRelativePath();
        $this->fileItem->setDestinationPaths(array($destination)); 
    }
}