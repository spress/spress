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

use Yosymfony\Spress\Configuration;

/**
 * Frontmatter is the configuration seccion of pages and posts.
 * The configuration is set between triple dashed lines.
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Frontmatter
{
    private $content;
    private $configuration;
    private $repository;
    private $pattern = '/^---\n(.*)\n?---\n?/isU';
    private $matches = array();
    private $result = false;
    
    /**
     * Constructor
     * 
     * @param string $content
     * @param Yosymfony\Spress\Configuration $configuration Transform Front-matter to repository
     */
    public function __construct($content, Configuration $configuration)
    {
        $this->content = $content;
        $this->configuration = $configuration;
        $this->process();
    }
    
    /**
     * The content has Front-matter?
     * 
     * @return bool
     */
    public function hasFrontmatter()
    {   
        return 1 === $this->result;
    }
    
    /**
     * @return string FALSE if any problem occurs
     */
    public function getFrontmatterString()
    {
        $result = false;
        
        if($this->hasFrontmatter())
        {
            $result = $this->matches[1];
        }
        
        return $result;
    }
    
    /**
     * @return array
     */
    public function getFrontmatterArray()
    {
        return $this->repository->getRaw();
    }
    
    /**
     * @return Yosymfony\Silex\ConfigServiceProvider\ConfigRepository
     */
    public function getFrontmatter()
    {
        return $this->repository;
    }
    
    /**
     * @return string FALSE if any problem occurs
     */
    public function getFrontmatterWithDashedLines()
    {
        $result = false;
        
        if ($this->hasFrontmatter())
        {
            $result = $this->matches[0];
        }
        
        return $result;
    }
    
    /**
     * Get content without Front-matter
     * 
     * @return string
     */
    public function getContentNotFrontmatter()
    {
        $result = preg_replace($this->pattern, '', $this->content, 1);
        return ltrim($result);
    }
    
    private function process()
    {
        $this->result = preg_match($this->pattern, $this->content, $this->matches);
        $this->repository = $this->configuration->getRepositoryInline($this->getFrontmatterString());
    }
}