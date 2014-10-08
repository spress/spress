<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\Plugin;

class PluginItem
{
    private $plugin;
    private $metas;
    
    public function __construct(PluginInterface $plugin)
    {
        $this->plugin = $plugin;
        $this->metas = $plugin->getMetas();
        
        if($this->metas && false === is_array($this->metas))
        {
            throw new \RuntimeException(sprintf('Invalid meta plugin at %s.', get_class($plugin)));
        }
    }
    
    /**
     * Get plugin instance
     * 
     * @return PluginInterface
     */
    public function getPlugin()
    {
        return $this->plugin;
    }
    
    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
        if($this->metas && isset($this->metas['name']))
        {
            return $this->metas['name'];
        }
        else
        {
            return get_class($this->plugin);
        }
    }
    
    /**
     * Get author
     * 
     * @return string
     */
    public function getAuthor()
    {
        return $this->metas && isset($this->metas['author']) ? $this->metas['author'] : '';
    }
    
    public function __toString()
    {
        return $this->getName();
    }
}