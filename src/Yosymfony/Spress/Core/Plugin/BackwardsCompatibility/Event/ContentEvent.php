<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Plugin\Event;

use Symfony\Component\EventDispatcher\Event;
use Yosymfony\Spress\Core\ContentManager\ContentItemInterface;

/**
 * Event base for events related with the content
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ContentEvent extends Event
{
    protected $item;
    protected $isPost;
    
    public function __construct(ContentItemInterface $item, $isPost = false)
    {
        $this->item = $item;
        $this->isPost = $isPost;
    }
    
    /**
     * Get item identifier
     * 
     * @return string
     */
    public function getId()
    {
        return $this->item->getId();
    }
    
    /**
     * Is post content?
     * 
     * @return bool
     */
    public function isPost()
    {
        return $this->isPost;
    }
    
    /**
     * Get the content without Front-matter
     * 
     * @return string
     */
    public function getContent()
    {
        switch($this->getName())
        {
            case SpressEvents::SPRESS_BEFORE_CONVERT:
                return $this->item->getPreConverterContent();
            case SpressEvents::SPRESS_AFTER_CONVERT:
            case SpressEvents::SPRESS_BEFORE_RENDER:
            case SpressEvents::SPRESS_BEFORE_RENDER_PAGINATION:
                return $this->item->getPostConverterContent();
            case SpressEvents::SPRESS_AFTER_RENDER:
            case SpressEvents::SPRESS_AFTER_RENDER_PAGINATION:
                return $this->item->getPostLayoutContent();
            default:
                return $this->item->getPreConverterContent();
        }
    }
    
    /**
     * Set the content without Front-matter
     * 
     * @param string $content
     */
    public function setContent($content)
    {
        switch($this->getName())
        {
            case SpressEvents::SPRESS_BEFORE_CONVERT:
                $this->item->setPreConverterContent($content);
                break;
            case SpressEvents::SPRESS_AFTER_CONVERT:
            case SpressEvents::SPRESS_BEFORE_RENDER:
            case SpressEvents::SPRESS_BEFORE_RENDER_PAGINATION:
                $this->item->setPostConverterContent($content);
                break;
            case SpressEvents::SPRESS_AFTER_RENDER:
            case SpressEvents::SPRESS_AFTER_RENDER_PAGINATION:
                $this->item->setPostLayoutContent($content);
                break;
            default:
                $this->item->setPreConverterContent($content);
        }
    }
    
    /**
     * Get relative path to the site, filename included.
     * 
     * @return string
     */
    public function getRelativePath()
    {
        return $this->item->getFileItem()->getRelativePathFilename();
    }
}