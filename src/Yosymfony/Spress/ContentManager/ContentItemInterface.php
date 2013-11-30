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
     * @return Yosymfony\Spress\ContentManager\Frontmatter
     */
    public function getFrontmatter();
    
    /**
     * Has Front-matter?
     * 
     * @return bool
     */
    public function hasFrontmatter();
    
    /**
     * Get content without Front-matter
     * 
     * @return string
     */
    public function getContent();
    
    /**
     * Set content
     * 
     * @param string $content
     */
    public function setContent($content);
    
    /**
     * Get the destination (transformed) content.
     * 
     * return string
     */
    //public function getDestinationContent();
    
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