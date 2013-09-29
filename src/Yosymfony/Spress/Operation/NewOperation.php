<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Operation;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

/**
 * @auhor Victor Puertas <vpgugr@gmail.com>
 */
class NewOperation
{
    private $templatePath;
    
    public function __construct($templatePath)
    {
        $this->templatePath = $templatePath;
    }
    
    /**
     * Create a new site scaffold
     * 
     * @param string $path
     * @param string $templateName
     * @param bool $force
     */
    public function newSite($path, $templateName, $force = false)
    {
        $fs = new Filesystem();
        $templatePath = $this->templatePath . '/' . $templateName;
        $existsPath = $fs->exists($path);
        $isEmpty = $this->isEmptyDir($path);
        
        if($existsPath && false == $force && false == $isEmpty)
        {
            throw new \RuntimeException(sprintf('Path "%s" exists and is not empty.', $path));
        }
        
        if(false == $fs->exists($templatePath))
        {
            throw new \InvalidArgumentException(sprintf('Template "%s" not exists', $templateName));
        }
        
        if($existsPath)
        {
            $fs->remove([$path]);
        }
        
        $fs->mirror($templatePath, $path);
    }
    
    /**
     * @return bool
     */
    private function isEmptyDir($path)
    {
        $finder = new Finder();
        $finder->in($path);
        
        $iterator = $finder->getIterator();
        $iterator->next();
        
        return !$iterator->valid();
    }
}