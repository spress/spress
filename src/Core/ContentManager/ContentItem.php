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
 * Base implementation for content items
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ContentItem implements ContentItemInterface
{
    protected $fileItem;
    protected $preConverterContent;
    protected $postConverterContent;
    protected $preLayoutContent;
    protected $postLayoutContent;
    protected $frontmatter;
    protected $configuration;
    protected $extension;

    /**
     * Constructor
     *
     * @param FileItem                       $fileItem
     * @param Yosymfony\Spress\Configuration $configuration
     */
    public function __construct(FileItem $fileItem, $configuration)
    {
        $this->fileItem = $fileItem;
        $this->extension = $fileItem->getExtension();
        $this->configuration = $configuration;
        $this->frontmatter = new Frontmatter($this->fileItem->getSourceContent(), $configuration);
        $this->setPreConverterContent($this->frontmatter->getContentNotFrontmatter());
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
     * @inheritDoc
     */
    public function hasFrontmatter()
    {
        return $this->frontmatter->hasFrontmatter();
    }

    /**
     * @inheritDoc
     */
    public function getFrontmatter()
    {
        return $this->frontmatter;
    }

    /**
     * @inheritDoc
     */
    public function getPreConverterContent()
    {
        return $this->preConverterContent;
    }

    /**
     * @inheritDoc
     */
    public function setPreConverterContent($content)
    {
        $this->preConverterContent = $content;
        $this->fileItem->setDestinationContent($content);
    }

    /**
     * @inheritDoc
     */
    public function getPostConverterContent()
    {
        return $this->postConverterContent;
    }

    /**
     * @inheritDoc
     */
    public function setPostConverterContent($content)
    {
        $this->postConverterContent = $content;
        $this->fileItem->setDestinationContent($content);
    }

    /**
     * @inheritDoc
     */
    public function getPreLayoutContent()
    {
        return $this->preLayoutContent;
    }

    /**
     * @inheritDoc
     */
    public function setPreLayoutContent($content)
    {
        $this->preLayoutContent = $content;
        $this->fileItem->setDestinationContent($content);
    }

    /**
     * @inheritDoc
     */
    public function getPostLayoutContent()
    {
        return $this->postLayoutContent;
    }

    /**
     * @inheritDoc
     */
    public function setPostLayoutContent($content)
    {
        $this->postLayoutContent = $content;
        $this->fileItem->setDestinationContent($content);
    }

    /**
     * @inheritDoc
     */
    public function setOutExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @inheritDoc
     */
    public function getFileItem()
    {
        return $this->fileItem;
    }
}
