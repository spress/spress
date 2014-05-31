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

use Yosymfony\Silex\ConfigServiceProvider\ConfigServiceProvider;
use Yosymfony\Spress\ContentLocator\ContentLocator;
use Yosymfony\Spress\ContentManager\ContentManager;
use Yosymfony\Spress\ContentManager\ConverterManager;
use Yosymfony\Spress\ContentManager\Renderizer;
use Yosymfony\Spress\IO\NullIO;
use Yosymfony\Spress\Plugin\PluginManager;
use Yosymfony\Spress\Operation\NewOperation;

/**
 * Spress Application
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Application extends \Silex\Application
{
    const VERSION = "1.1.0";
    
    public function __construct(array $values = array())
    {
        parent::__construct($values);
        
        $spressPath = realpath(dirname(__FILE__) . '/../../../');
        $templatesPath = $this->getTemplatesPath($spressPath);
        
        // Paths and filenames standard
        $this['spress.paths'] = array(
            'root'            => $spressPath,
            'config'          => $spressPath  . '/app/config',
            'config.file'     => 'config.yml',
            'config.file_env' => 'config_:env.yml',
            'templates'       => $templatesPath,
            'web'             => $spressPath  . '/web',
            'web.index'       => $spressPath  . '/web/index.php',
        );
        $this['spress.version'] = self::VERSION;
        
        if(false == isset($this['spress.io']))
        {
            $this['spress.io'] = $this->share(function($app){
                return new NullIO();
            });
        }
        
        $this->register(new ConfigServiceProvider(array($this['spress.paths']['config'])));
        
        $this['spress.config'] = $this->share(function($app){
            return new Configuration($app['configuration'], $app['spress.paths'], $app['spress.version']);
        });
        
        $this['spress.content_locator'] = $this->share(function($app){
            return new ContentLocator($app['spress.config']);
        });

        $this['spress.twig_factory'] = $this->share(function(){
            return new TwigFactory();
        });
        
        $this['spress.cms.converter'] = $this->share(function($app){
            return new ConverterManager(
            $app['spress.config'],
            [
                new \Yosymfony\Spress\ContentManager\Converter\Markdown(),
                new \Yosymfony\Spress\ContentManager\Converter\Mirror(),
            ]);
        });
        
        $this['spress.cms.plugin'] = $this->share(function($app){
            return new PluginManager(
                $app['spress.content_locator'],
                $app['spress.cms.plugin.classLoader']);
        });
        
        $this['spress.cms.plugin.classLoader'] = function()
        {
            $autoloaders = spl_autoload_functions();
            
            return $autoloaders[0][0];
        };
        
        $this['spress.cms.renderizer'] = $this->share(function($app){
            return new Renderizer(
                $app['spress.twig_factory'],
                $app['spress.content_locator'],
                $app['spress.config']);
        });
        
        $this['spress.cms'] = $this->share(function($app){
            return new ContentManager(
                $app['spress.cms.renderizer'],
                $app['spress.config'],
                $app['spress.content_locator'],
                $app['spress.cms.converter'],
                $app['spress.cms.plugin'],
                $app['spress.io']);
        });
        
        $this['spress.operation.new'] = $this->share(function($app){
            return new NewOperation($app['spress.paths']['templates']);
        });
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
    public function parse($localConfigPath = null, $env = 'dev', $timezone = null, $drafts = null, $safe = null)
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
    
    /**
     * @return string
     */
    private function getTemplatesPath($spressPath)
    {
        if(file_exists($spressPath . '/app/templates/'))
        {
            return $spressPath . '/app/templates';
        }
        
        return realpath($spressPath . '/../spress-templates');
    }
}