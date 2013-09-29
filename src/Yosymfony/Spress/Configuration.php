<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress;

use Yosymfony\Silex\ConfigServiceProvider\Config;
use Yosymfony\Silex\ConfigServiceProvider\ConfigRepository;

 /**
  * Configuration manager
  * 
  * @author Victor Puertas <vpgugr@gmail.com>
  */
class Configuration
{
    private $configService;
    private $paths;
    private $version;
    private $repository;
    private $globalRepository;
    private $localRepository;
    
    /**
     * Constructor
     * 
     * @param Config $configService Config service
     * @param array $paths Spress paths and filenames standard
     */
    public function __construct(Config $configService, array $paths, $version)
    {
        $this->configService = $configService;
        $this->paths = $paths;
        $this->version = $version;
        $this->loadGlobalRepository();
        $this->repository = $this->globalRepository;
    }
    
    /**
     * Load the local configuration
     * 
     * @param string $localPath
     */
    public function loadLocal($localPath = null)
    {
        $this->loadLocalRepository($localPath);
        $this->checkDefinitions($this->repository);
    }
    
    /**
     * Get a repository from string
     * 
     * @param string $config Configuration
     * 
     * @return ConfigRepository
     */
    public function getRepositoryInline($config)
    {
        return $this->configService->load($config, Config::TYPE_YAML);
    }
    
    /**
     * Create a blank config repository
     * 
     * @return Yosymfony\Silex\ConfigServiceProvider\ConfigRepository
     */
    public function createBlankRepository()
    {
        return new ConfigRepository();
    }
    
    /**
     * Get the local repository merged with global repository
     * 
     * @return ConfigRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
     
    /**
     * Get the global repository
     * 
     * @return ConfigRepository
     */
    public function getGlobal()
    {
        return $this->globalRepository;
    }
    
    /**
     * Get the local repository
     * 
     * @return ConfigRepository
     */
    public function getLocal()
    {
        return $this->localRepository;
    }
    
    /**
     * Get the config filename. Typically "config.yml"
     * 
     * @return string
     */
    public function getConfigFilename()
    {
        return $this->paths['config.file'];
    }
    
    /**
     * For internal: Get the standard paths and filenames of app.
     * 
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }
    
    /**
     * Get Spress version
     * 
     * @return string
     */
    public function getAppVersion()
    {
        return $this->version;
    }
    
    private function loadGlobalRepository()
    {
        $this->globalRepository = $this->configService->load($this->getConfigFilename());
        $this->checkDefinitions($this->globalRepository);
    }
    
    private function loadLocalRepository($localPath)
    {
        $localConfigPath = $this->resolveConfigLocalPath($localPath);
        $this->localRepository = $this->configService->load($localConfigPath);
        $this->localRepository['source'] = $this->resolvePath($localPath);
        
        $this->repository = $this->localRepository->mergeWith($this->globalRepository);
    }
    
    private function resolveConfigLocalPath($localPath)
    {
        if($localPath)
        {
            $localPath = $localPath. '/' . $this->paths['config.file'];
        }
        else
        {
            $localPath = $this->globalRepository['source'] . '/' .$this->paths['config.file'];
        }
        
        return $this->resolvePath($localPath);
    }
    
    private function resolvePath($path)
    {
        $realPath = realpath($path);
        
        if(false === $realPath)
        {
            throw new \InvalidArgumentException(sprintf('Invalid path "%s"', $path));
        }
        
        return $realPath;
    }
    
    /**
     * Check the definition of standard configuration keys
     */
    private function checkDefinitions($repository)
    {
        // todo: Check the $repository definitions.
    }
}