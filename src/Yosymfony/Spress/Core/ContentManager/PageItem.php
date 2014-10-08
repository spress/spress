<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\ContentManager;

use Yosymfony\Spress\Core\ContentLocator\FileItem;

/**
 * Content of a page
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class PageItem extends ContentItem
{
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
        
        parent::__construct($fileItem, $configuration);
        
        $this->setUpDestinationPath();
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
     * Get item payload. The 'content' var has been set
     * with PostConverterContent
     * 
     * @return array
     */
    public function getPayload()
    {
        $fm = $this->frontmatter->getFrontmatter();
        $repository = $this->configuration->createBlankRepository();
        $repository->set('url', $this->getUrl());
        $repository->set('content', $this->getPostConverterContent());
        $repository->set('id', $this->getId());
        $repository->set('path', $this->getRelativePath());
        
        return $repository->union($fm)->getArray();
    }
    
    /**
     * Set out extension
     * 
     * @param string $extension
     */
    public function setOutExtension($extension)
    {
        parent::setOutExtension($extension);
        
        $this->setUpDestinationPath();
    }
    
    /**
     * @return string
     */
    private function getUrlTemplate()
    {
        $template = '/:path/:basename.:extension';
        $filename = $this->fileItem->getFileName(false);
        $extension = $this->extension;
        $permalinkStyle = $this->configuration->getRepository()->get('permalink');
        
        if('pretty' == $permalinkStyle)
        {
            if('index' == $filename && 'html' == $extension)
            {
                $template = '/:path/';
            }
            else if('html' == $extension)
            {
                $template = '/:path/:basename/';
            }
        }
        
        return $template;
    }
    
    /**
     * @return array
     */
    private function getUrlPlaceholders()
    {
        return [
            ':path'         => $this->fileItem->getRelativePath(),
            ':basename'     => $this->fileItem->getFileName(false),
            ':extension'    => $this->extension,
        ];
    }
    
    /**
     * @return string
     */
    private function getFilename()
    {
       return $this->fileItem->getFileName(false) . '.' . $this->extension; 
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
