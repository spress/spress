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

/**
 * Iterface for content items
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface ContentItemInterface
{
    /**
     * Get item identifier
     * 
     * @return mixed
     */
    public function getId();
    
    /**
     * Get Front-matter
     * 
     * @return Yosymfony\Silex\ConfigServiceProvider\ConfigRepository
     */
    public function getFrontmatter();
    
    /**
     * Has Front-matter?
     * 
     * @return bool
     */
    public function hasFrontmatter();
    
    /**
     * Get source content without metadate like Front-matter
     * 
     * @return string
     */
    public function getContent();
    
    /**
     * Get the destination (transformed) content.
     * 
     * return string
     */
    public function getDestinationContent();
    
    /**
     * Set converted content
     * 
     * @param string $content
     */
    public function setConvertedContent($content);
    
    /**
     * Set rendered content
     * 
     * @param string $content
     */
    public function setRenderedContent($content);
    
    /**
     * Set out extension
     * 
     * @param string $extension
     */
    public function setOutExtension($extension);
    
    /**
     * Get the FileItem associated
     * 
     * @return FileItem
     */
    public function getFileItem();
}