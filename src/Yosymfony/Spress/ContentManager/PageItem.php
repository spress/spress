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

use Yosymfony\Spress\ContentLocator\FileItem;

/**
 * Page content
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PageItem implements ContentItemInterface
{
    private $fileItem;
    private $frontmatter;
    private $configuration;
    private $content;
    
    /**
     * Constructor
     * 
     * @param FileItem $fileItem
     * @param Yosymfony\Spress\Configuration $configuration
     */
    public function __construct(FileItem $fileItem, $configuration)
    {
        if($fileItem->getType() !== FileItem::TYPE_PAGE)
        {
            throw new \InvalidArgumentException(sprintf('Type item "%s" is invalid in page item.', $fileItem->getType()));
        }
        
        $this->configuration = $configuration;
        $this->fileItem = $fileItem;
        $this->frontmatter = new Frontmatter($this->fileItem->getSourceContent(), $configuration);
        $this->content = $this->frontmatter->getContentNotFrontmatter();
        $this->setDestinationContent($this->content);
        $this->setUpDestinationPath();
    }
    
    /**
     * Get source content without Frontmatter
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
        $generator = new UrlGenerator();
        $url = $generator->getUrl($this->getUrlTemplate(), $this->getUrlPlaceholders());
        
        return $url;
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
    public function setDestinationContent($content)
    {
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
        $repository->set('url', $this->getUrl());
        $repository->set('content', $this->getContent());
        $repository->set('id', $this->getId());
        $repository->set('path', $this->getRelativePath());
        
        return $repository->mergeWith($fm)->getRaw();
    }
    
    /**
     * Get the FileItem associated (from ContentItemInterface)
     * 
     * @return FileItem
     */
    public function getFileItem()
    {
        return $this->fileItem;
    }
    
    /**
     * @return string
     */
    private function getUrlTemplate()
    {
        $template = '/:path/:basename.:extension';
        $filename = $this->fileItem->getFileName(false);
        $extension = $this->getOutExtension();
        $permalinkStyle = $this->configuration->getRepository()->get('permalink');
        
        if('pretty' == $permalinkStyle)
        {
            if('index' == $filename && 'html' == $extension)
            {
                $template = '/:path';
            }
            else if('html' == $extension)
            {
                $template = '/:path/:basename';
            }
        }
        
        return $template;
    }
    
    /**
     * @return @array
     */
    private function getUrlPlaceholders()
    {
        return [
            ':path'         => $this->fileItem->getRelativePath(),
            ':basename'     => $this->fileItem->getFileName(false),
            ':extension'    => $this->getOutExtension(),
        ];
    }
    
    /**
     * @return bool
     */
    private function isMarkdown()
    {
        return in_array($this->fileItem->getExtension(), $this->configuration->getRepository()->get('markdown_ext'));
    }
    
    /**
     * @return string
     */
    private function getOutExtension()
    {
        $extension = $this->fileItem->getExtension();
        $isTransformedMarkdown = $this->isMarkdown() && $this->hasFrontmatter();
        
        return 'html' === $extension || $isTransformedMarkdown ? 'html' : $extension;
    }
    
    /**
     * @return string
     */
    private function getFilename()
    {
       return $this->fileItem->getFileName(false) . '.' . $this->getOutExtension(); 
    }
    
    /**
     * @return string
     */
    private function getRelativePath()
    {
        $relativePath = $this->fileItem->getRelativePath();
        
        return strlen($relativePath) > 0 ? $relativePath. '/'. $this->getFilename() : $this->getFilename();
    }
    
    private function setUpDestinationPath()
    {
        $destination = $this->getRelativePath();
        $this->fileItem->setDestinationPaths(array($destination)); 
    }
}