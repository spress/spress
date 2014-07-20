<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core;

use Yosymfony\ConfigLoader\Config;
use Yosymfony\ConfigLoader\Repository;
use Yosymfony\Spress\Core\Definition\ConfigDefinition;

 /**
  * Configuration manager
  * 
  * @author Victor Puertas <vpgugr@gmail.com>
  */
class Configuration
{
    private $configLoader;
    private $paths;
    private $version;
    private $repository;
    private $globalRepository;
    private $localRepository;
    private $envRepository;
    private $envName;
    
    /**
     * Constructor
     * 
     * @param Yosymfony\ConfigLoader\Config $configLoader
     * @param array $paths Spress paths and filenames standard
     * @param string $version App version
     */
    public function __construct(Config $configLoader, array $paths, $version)
    {
        $this->configLoader = $configLoader;
        $this->paths = $paths;
        $this->version = $version;
        $this->envName = 'dev';
        $this->loadGlobalRepository();
        $this->repository = $this->globalRepository;
        $this->localRepository = $this->createBlankRepository();
        $this->envRepository = $this->createBlankRepository();
    }
    
    /**
     * Load the local configuration, environtment included
     * 
     * @param string $localPath File configuration of the site
     * @param string $env Environment name. If null take the value from global config.yml
     */
    public function loadLocal($localPath = null, $env = null)
    {
        if($env)
        {
            $this->envName = $env;
        }

        $this->loadLocalRepository($localPath);
        $this->loadEnvironmentRepository($localPath, $this->envName);
        
        $tmpRepository = $this->localRepository->union($this->globalRepository);
        $this->repository = $this->envRepository->union($tmpRepository);
        
        if($env)
        {
            $this->repository['env'] = $this->envName;
        }    
        
        $this->checkDefinitions($this->repository);
    }
    
    /**
     * Get a repository from string
     * 
     * @param string $config Configuration
     * 
     * @return Yosymfony\ConfigLoader\Repository
     */
    public function getRepositoryInline($config)
    {
        return $this->configLoader->load($config, Config::TYPE_YAML);
    }
    
    /**
     * Create a blank config repository
     * 
     * @return Yosymfony\ConfigLoader\Repository
     */
    public function createBlankRepository()
    {
        return new Repository();
    }
    
    /**
     * Get the environment repository merged with local repository and global repository
     * 
     * @return Yosymfony\ConfigLoader\Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }
     
    /**
     * Get the global repository
     * 
     * @return Yosymfony\ConfigLoader\Repository
     */
    public function getGlobal()
    {
        return $this->globalRepository;
    }
    
    /**
     * Get the local repository
     * 
     * @return Yosymfony\ConfigLoader\Repository
     */
    public function getLocal()
    {
        return $this->localRepository;
    }
    
    /**
     * Get the environment repository
     * 
     * @return Yosymfony\ConfigLoader\Repository
     */
    public function getEnvironment()
    {
        return $this->envRepository;
    }
    
    /**
     * Get the environment name
     * 
     * @return string
     */
    public function getEnvironmentName()
    {
        return $this->envName;
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
     * Get the config environment filename.
     * 
     * @return string Null if the environment is dev
     */
    public function getConfigEnvironmentFilename()
    {
        return $this->getConfigEnvFilename($this->envName);
    }
    
    /**
     * Get the config environment filename with wildcard: "config_*.yml"
     * 
     * @return string
     */
    public function getConfigEnvironmentFilenameWildcard()
    {
        return str_replace(':env', '*', $this->paths['config.file_env']);
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
    
    /**
     * For internal purpose: get the standard paths and filenames of the app.
     * 
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }
    
    private function loadGlobalRepository()
    {
        $this->globalRepository = $this->configLoader->load($this->getConfigFilename());
        $this->checkDefinitions($this->globalRepository);
        
        $this->envName = $this->globalRepository['env'];
    }
    
    /**
     * @param string $localPath
     */
    private function loadLocalRepository($localPath)
    {
        $filename = $this->paths['config.file'];
        $localConfigPath = $this->resolveLocalPath($localPath, $filename);
        $this->localRepository = $this->configLoader->load($localConfigPath);
        $this->localRepository['source'] = $this->resolvePath($localPath);
    }
    
    /**
     * @param string $localPath
     * @param string $env
     */
    private function loadEnvironmentRepository($localPath, $env)
    {
        $filename = $this->getConfigEnvFilename($env);
        
        if($filename)
        {
            $localPath = $this->getLocalPathFilename($localPath, $filename);
            $resolvedPath = $this->resolvePath($localPath, false);
            
            if($resolvedPath)
            {
                $this->envRepository = $this->configLoader->load($resolvedPath);
            }
        }
    }
    
    /**
     * @param string $env
     * 
     * @return string
     */
    private function getConfigEnvFilename($env)
    {
        if('dev' === strtolower($env))
        {
            return;
        }
        
        $filenameTemplate = $this->paths['config.file_env'];
        $filename = str_replace(':env', $env, $filenameTemplate);
        
        return $filename;
    }
    
    /**
     * @param string $localPath
     * @param string $filename
     * 
     * @return string
     */
    private function getLocalPathFilename($localPath, $filename)
    {
        if($localPath)
        {
            return $localPath. '/' . $filename;
        }
        else
        {
            return $this->globalRepository['source'] . '/' . $filename;
        }
    }
    
    /**
     * @param string $localPath
     * @param string $filename
     * 
     * @return string
     */
    private function resolveLocalPath($localPath, $filename)
    {
        $path = $this->getLocalPathFilename($localPath, $filename);
        
        return $this->resolvePath($path);
    }
    
    /**
     * @param string $path
     * @param boolean $throwException
     * 
     * @return string
     */
    private function resolvePath($path, $throwException = true)
    {
        $realPath = realpath($path);
        
        if(false === $realPath && true === $throwException)
        {
            throw new \InvalidArgumentException(sprintf('Invalid path "%s"', $path));
        }
        
        return $realPath;
    }
    
    /**
     * @param Repository $repository
     */
    private function checkDefinitions(Repository $repository)
    {
        $intersection = $repository->intersection($this->globalRepository);
        $intersection->validateWith(new ConfigDefinition());
    }
}