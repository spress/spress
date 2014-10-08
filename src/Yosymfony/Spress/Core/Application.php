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

use Pimple\Container;
use Symfony\Component\Config\FileLocator;
use Yosymfony\ConfigLoader\Config;
use Yosymfony\Spress\Core\ContentLocator\ContentLocator;
use Yosymfony\Spress\Core\ContentManager\ContentManager;
use Yosymfony\Spress\Core\ContentManager\ConverterManager;
use Yosymfony\Spress\Core\ContentManager\Renderizer;
use Yosymfony\Spress\Core\IO\NullIO;
use Yosymfony\Spress\Core\Plugin\PluginManager;

/**
 * Spress Application
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Application extends Container
{
    const VERSION = "1.1.0";
    
    public function __construct(array $options = [])
    {
        parent::__construct();
        
        $this['spress.version'] = self::VERSION;
        
        // Paths and filenames standard
        $this['spress.paths'] = [
            'config'          => realpath(dirname(__FILE__)) . '/config',
            'config.file'     => 'config.yml',
            'config.file_env' => 'config_:env.yml',
        ];
        
        if(isset($options['spress.paths']))
        {
            $this['spress.paths'] = array_replace($this['spress.paths'], $options['spress.paths']);
        }

        $this['spress.io'] = function($app){
                return new NullIO();
        };
        
        if(isset($options['spress.io']))
        {
            $this['spress.io'] = $options['spress.io'];
        }
        
        $this['configuration'] = function($app){
            $locator = new FileLocator([$this['spress.paths']['config']]);
            
            return new Config([
                new \Yosymfony\ConfigLoader\Loaders\YamlLoader($locator),
                new \Yosymfony\ConfigLoader\Loaders\JsonLoader($locator),
            ]);
        };

        $this['spress.config'] = function($app){
            return new Configuration($app['configuration'], $app['spress.paths'], $app['spress.version']);
        };
        
        $this['spress.content_locator'] = function($app){
            return new ContentLocator($app['spress.config']);
        };
        
        $this['spress.cms.converter'] = function($app){
            return new ConverterManager(
            $app['spress.config'],
            [
                new \Yosymfony\Spress\Core\ContentManager\Converter\Markdown(),
                new \Yosymfony\Spress\Core\ContentManager\Converter\Mirror(),
            ]);
        };
        
        $this['spress.cms.plugin.classLoader'] = function()
        {
            $autoloaders = spl_autoload_functions();
            
            return $autoloaders[0][0];
        };
        
        $this['spress.cms.plugin.options'] = [
            'vendors_dir'       => 'vendors',
            'composer_filename' => 'composer.json',
        ];
        
        $this['spress.cms.plugin'] = function($app){
            return new PluginManager(
                $app['spress.content_locator'],
                $app['spress.cms.plugin.classLoader'],
                $app['spress.cms.plugin.options']);
        };
        
        $this['spress.cms.renderizer'] = function($app){
            return new Renderizer(
                $app['spress.content_locator'],
                $app['spress.config']);
        };
        
        $this['spress.cms'] = function($app){
            return new ContentManager(
                $app['spress.cms.renderizer'],
                $app['spress.config'],
                $app['spress.content_locator'],
                $app['spress.cms.converter'],
                $app['spress.cms.plugin'],
                $app['spress.io']);
        };
    }
    
    /**
     * Parse a site
     * 
     * @param string $localConfigPath Path of the local configuration
     * @param string $env Environment name
     * @param string $timezone Set the timezone
     * @param bool $drafts Include draft
     * @param bool $safe Plugins disabled
     * 
     * @return array Key-value result
     */
    public function parse($localConfigPath = null, $env = null, $timezone = null, $drafts = null, $safe = null)
    {
        $this['spress.config']->loadLocal($localConfigPath, $env);
        
        if(null !== $drafts && is_bool($drafts))
        {
            $this['spress.config']->getRepository()->set('drafts', $drafts);
        }
        
        if(null !== $timezone && is_string($timezone))
        {
            $this['spress.config']->getRepository()->set('timezone', $timezone);
        }

        if(null !== $safe && is_bool($safe))
        {
            $this['spress.config']->getRepository()->set('safe', $safe);
        }
        
        return $this['spress.cms']->processSite();
    }
}
