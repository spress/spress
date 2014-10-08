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
     * Get original content without Front-matter
     * 
     * @return string
     */
    public function getPreConverterContent();
    
    /**
     * Set the original content
     * 
     * @param string $content
     */
    public function setPreConverterContent($content);
    
    /**
     * Get the content after converter applied
     * 
     * @return string
     */
    public function getPostConverterContent();
    
    /**
     * Set the content with the converter applied.
     * In this phase Twig tags don't have been resolved.
     * 
     * @param string $content
     */
    public function setPostConverterContent($content);
    
    /**
     * Get the content without layout.
     * The Twig tags of the content have been resolved.
     * 
     * @return string
     */
    public function getPreLayoutContent();
    
    /**
     * Set the content with the the Twig tag resolved
     * 
     * @param string $content
     */
    public function setPreLayoutContent($content);
    
    /**
     * Get content with the layout applied
     * 
     * @return string
     */
    public function getPostLayoutContent();
    
    /**
     * Set the content with the layout applied
     * 
     * @param string $content
     */
    public function setPostLayoutContent($content);
    
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
