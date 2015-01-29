<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentLocator;

use Symfony\Component\Finder\SplFileInfo;

/**
 * File information
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class FileItem
{
    const TYPE_POST = 'post';
    const TYPE_PAGE = 'page';

    private $type;
    private $destinationPaths;
    private $fileInfo;
    private $sourceContent;
    private $destinationContent;

    /**
     * Constructor
     *
     * @param SplFileInfo $fileInfo from Symfony Finder
     * @param int Type of item: FileItem::TYPE_POST or FileItem::TYPE_PAGE
     */
    public function __construct(SplFileInfo $fileInfo, $type)
    {
        $this->fileInfo = $fileInfo;
        $this->type = $type;
    }

    /**
     * Get Source content
     *
     * @param bool $reload Reload content fron source
     *
     * @return string Content
     *
     * @throws \RuntimeException
     */
    public function getSourceContent($reload = false)
    {
        if (null === $this->sourceContent || $reload) {
            $this->sourceContent = $this->fileInfo->getContents();
        }

        return $this->sourceContent;
    }

    /**
     * Set the destination content
     *
     * @param string $content
     */
    public function setDestinationContent($content)
    {
        $this->destinationContent = $content;
    }

    /**
     * Get the destination content
     *
     * @return string
     */
    public function getDestinationContent()
    {
        return $this->destinationContent;
    }

    /**
     * Set the destination paths. You can save this item in multiple paths
     *
     * @param array $paths
     */
    public function setDestinationPaths(array $paths)
    {
        return $this->destinationPaths = $paths;
    }

    /**
     * Gest the destination paths.
     *
     * @return array $paths
     */
    public function getDestinationPaths()
    {
        return $this->destinationPaths;
    }

    /**
     * Get file relative paths (without filename). If your file are in
     * _/post/news/file.md the method return "news"
     *
     * @return string
     */
    public function getRelativePath()
    {
        return $this->fileInfo->getRelativePath();
    }

    /**
     * Get file relative paths with filename. If your file are in
     * _/post/news/file.md the method return "news/file.md"
     *
     * @return string
     */
    public function getRelativePathFilename()
    {
        return $this->fileInfo->getRelativePathname();
    }

    /**
     * Get the parts of relative path (without filename).
     * It use DIRECTORY_SEPARATOR const
     *
     * @return array
     */
    public function getRelativePathExplode()
    {
        $path = $this->fileInfo->getRelativePath();
        $result = explode(DIRECTORY_SEPARATOR, $path);

        if ('' === $result[0]) {
            unset($result[0]);
        }

        return $result;
    }

    /**
     * Get the filename
     *
     * @param bool $withExtension Include filename extension
     *
     * @return string
     */
    public function getFileName($withExtension = true)
    {
        return $this->fileInfo->getBasename($withExtension ? '' : '.'.$this->getExtension());
    }

    /**
     * Get filename extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->fileInfo->getExtension();
    }

    /**
     * Get the item type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Real path
     *
     * @return string
     */
    public function __tostring()
    {
        return $this->fileInfo->getRealpath();
    }
}
