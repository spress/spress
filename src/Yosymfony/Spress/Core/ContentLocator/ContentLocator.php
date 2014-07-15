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

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\Spress\Core\Configuration;

/**
 * Locate the content of a site
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ContentLocator
{
    private $configuration;
    private $convertersExtension = [];
    
    /**
     * Constructor
     * 
     * @param Configuration $configuration Configuration manager
     */
    public function __construct(Configuration $configuration)
    {
        if(null === $configuration)
        {
            throw new \InvalidArgumentException('Configuration is null');
        }
        
        $this->configuration = $configuration;
        $this->setCurrentDir($this->getSourceDir());
        $this->createDestinationDirIfNotExists();
    }
    
    /**
     * @param array $extensions
     */
    public function setConvertersExtension(array $extensions)
    {
        $this->convertersExtension = $extensions;
    }
    
    /**
     * Get the site posts
     * 
     * @return array Array of FileItem
     */
    public function getPosts()
    {
        $posts = [];

        if(0 == count($this->convertersExtension))
        {
            return $posts;
        }

        if($this->getPostsDir())
        {
            $finder = new Finder();
            $finder->in($this->getPostsDir())->files();
            $finder->name($this->fileExtToRegExpr($this->convertersExtension));
            
            foreach($finder as $file)
            {
                $posts[] = new FileItem($file, FileItem::TYPE_POST);
            }
        }
        
        return $posts;
    }
    
    /**
     * Get the site pages. Page items is the files with extension in 
     * "processable_ext" key. This method uses "include" and "exclude" keys
     * from config.yml
     * 
     * @return array Array of FileItem
     */
    public function getPages()
    {
        $items = array();
        $includedFiles = array();
        $exclude = $this->configuration->getRepository()->get('exclude');
        $include = $this->configuration->getRepository()->get('include');
        $processableExt = $this->getProcessableExtention();
        
        if(0 == count($processableExt))
        {
            return $items;
        }

        $finder = new Finder();
        $finder->in($this->getSourceDir())->exclude($this->getSpecialDir())->files();
        $finder->name($this->fileExtToRegExpr($processableExt));

        foreach($include as $item)
        {
            if(is_dir($item))
            {
                $finder->in($item);
            }
            else if(is_file($item) && in_array(pathinfo($item, PATHINFO_EXTENSION), $processableExt))
            {
                $includedFiles[] = new SplFileInfo($this->resolvePath($item), "", pathinfo($item, PATHINFO_BASENAME));
            }
        } 
        
        $finder->append($includedFiles);
        
        foreach($exclude as $item)
        {
            $finder->notPath($item);
        }

        foreach($finder as $file)
        {
            $items[] = new FileItem($file, FileItem::TYPE_PAGE);
        }
        
        return $items;
    }
    
    /**
     * Get a filename
     * 
     * @return FileItem
     */
    public function getItem($path)
    {
        $fs = new Filesystem();
        
        if($fs->exists($path))
        {
            $relativePath = $fs->makePathRelative($path, $this->getSourceDir());
            $filename = pathinfo($path, PATHINFO_BASENAME);
            
            if(false !== strpos($relativePath, '..'))
            {
                $relativePath = "";
            }
            
            $fileInfo = new SplFileInfo($path, $relativePath, $relativePath . $filename);
            
            return new FileItem($fileInfo, FileItem::TYPE_PAGE);
        }
    }
    
    /**
     * Get the site layouts
     * 
     * @return array of FileItem
     */
    public function getLayouts()
    {
        $result = [];
        
        if($this->getLayoutsDir())
        {
            $finder = new Finder();
            $finder->in($this->getLayoutsDir())->files();
            
            foreach($finder as $file)
            {
                $result[$file->getRelativePathname()] = new FileItem($file, FileItem::TYPE_PAGE);
            }
        }
        
        return $result;
    }
    
    /**
     * Save a FileItem into destiny paths
     * 
     * @param FileItem $item
     */
    public function saveItem(FileItem $item)
    {
        $fs = new Filesystem();
        $paths = $item->getDestinationPaths();
        
        if(0 == count($paths))
        {
            throw new \LengthException('No destination paths found');
        }
        
        foreach($paths as $destination)
        {
            $fs->dumpFile($this->getDestinationDir() . '/' . $destination, $item->getDestinationContent());
        }
    }
    
    /**
     * Copy the rest files to destination
     * 
     * @return array Filenames affected
     */
    public function copyRestToDestination()
    {
        $fs = new Filesystem();
        $result = array();
        $includedFiles = array();
        $dir = $this->getSourceDir();
        $include = $this->configuration->getRepository()->get('include');
        $exclude = $this->configuration->getRepository()->get('exclude');
        $processableExt = $this->getProcessableExtention();
        
        $finder = new Finder();
        $finder->in($dir)->exclude($this->getSpecialDir());
        $finder->notName($this->configuration->getConfigFilename());
        $finder->notName($this->configuration->getConfigEnvironmentFilenameWildcard());
        $finder->notName($this->fileExtToRegExpr($processableExt));
        
        foreach($include as $item)
        {
            if(is_dir($item))
            {
                $finder->in($item);
            }
            else if(is_file($item) && !in_array(pathinfo($item, PATHINFO_EXTENSION), $processableExt))
            {
                $includedFiles[] = new SplFileInfo($this->resolvePath($item), "", pathinfo($item, PATHINFO_BASENAME));
            }
        }
        
        foreach($exclude as $item)
        {
            $finder->notPath($item);
        }
        
        $finder->append($includedFiles);

        foreach($finder as $file)
        {
            if($file->isDir())
            {
                $this->mkDirIfNotExists($this->getDestinationDir() . '/' . $file->getRelativePath());
            }
            else if($file->isFile())
            {
                $result[] = $file->getRealpath();
                $fs->copy($file->getRealpath(), $this->getDestinationDir() . '/' . $file->getRelativePathname());
            }
        }
        
        return $result;
    }
    
    /**
     * Remove all files and directories from destination directory
     * 
     * @return array List of deleted elements
     * 
     * @throw IOException When removal fails
     */
    public function cleanupDestination()
    {
        $fs = new Filesystem();
        $destinationDir = $this->configuration->getRepository()->get('destination');
        
        $finder = new Finder();
        $finder->in($destinationDir)
            ->depth('== 0');
        
        $fs->remove($finder);
    }
    
    /**
     * Get the absolute path of posts directory
     * 
     * @return string
     */
    public function getPostsDir()
    {
        return $this->resolvePath($this->configuration->getRepository()->get('posts'));
    }
    
    /**
     * Get the absolute path of source directory
     * 
     * @return string
     */
    public function getSourceDir()
    {
        return $this->configuration->getRepository()->get('source');
    }
    
    /**
     * Get the absolute paths of destination directory
     * 
     * @return string
     */
    public function getDestinationDir()
    {
        return $this->resolvePath($this->configuration->getRepository()->get('destination'));
    }
    
    /**
     * Get the absolute paths of includes directory
     */
    public function getIncludesDir()
    {
        return $this->resolvePath($this->configuration->getRepository()->get('includes'));
    }
    
    /**
     * Get the absolute paths of layouts directory
     */
    public function getLayoutsDir()
    {
        return $this->resolvePath($this->configuration->getRepository()->get('layouts'));
    }
    
    /**
     * Get the absolute path of plugins directory
     * 
     * @return string
     */
    public function getPluginDir()
    {
        return $this->resolvePath($this->configuration->getRepository()->get('plugins'));
    }
    
    /**
     * Get the processable file's extension. It's a union result
     * between 'processable_ext' key and extensions registered by
     * converters
     * 
     * @return array
     */
    public function getProcessableExtention()
    {
        $processableExt = $this->configuration->getRepository()->get('processable_ext');
        
        return array_unique(array_merge($processableExt, $this->convertersExtension));
    }
    
    private function fileExtToRegExpr(array $extensions)
    {
        $list = array_reduce($extensions, function($a, $item){
            return $a . '|' . $item;
        });
        
        return '/\.(' . $list . ')$/';
    }
    
    private function setCurrentDir($path)
    {
        if(false === chdir($path))
        {
            throw new \InvalidArgumentException(sprintf('Error when change the current dir to "%s"', $path));
        }
    }
    
    private function mkDirIfNotExists($path)
    {
        $fs = new Filesystem();
        
        if(!$fs->exists($path))
        {
            $fs->mkdir($path);
        }
    }
    
    /**
     * @return string
     */
    private function resolvePath($path)
    {
        $realPath = realpath($path);
        
        if(false === $realPath)
        {
            return '';
        }
        
        return $realPath;
    }
    
    private function getSpecialDir()
    {
        if (false == isset($this->specialDirs))
        {
            $this->specialDirs = [];
            
            $finder = new Finder();
            $finder->in($this->getSourceDir())
                ->directories()
                ->path('/^_/')
                ->depth('== 0');
    
            foreach($finder as $file)
            {
                $this->specialDirs[] = $file->getRelativePathname();
            }
        }

        return $this->specialDirs;
    }
    
    private function createDestinationDirIfNotExists()
    {
        $fs = new Filesystem();
        $destination = $this->configuration->getRepository()->get('destination');
        
        if(false == $fs->exists($destination))
        {
            $fs->mkdir($destination);
        }
    }
}