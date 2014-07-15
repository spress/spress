<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Plugin\Event;

use Symfony\Component\EventDispatcher\Event;
use Yosymfony\Spress\Core\Configuration;
use Yosymfony\Spress\Core\ContentManager\ConverterInterface;
use Yosymfony\Spress\Core\ContentManager\ConverterManager;
use Yosymfony\Spress\Core\ContentManager\Renderizer;
use Yosymfony\Spress\Core\ContentLocator\ContentLocator;
use Yosymfony\Spress\Core\Plugin\API\TemplateManager;
use Yosymfony\Spress\Core\IO\IOInterface;

class EnvironmentEvent extends Event implements EnviromentEvent
{
    private $configuration;
    private $converter;
    private $renderizer;
    private $contentLocator;
    private $io;
    
    public function __construct(
        Configuration $configuration, 
        ConverterManager $converter, 
        Renderizer $renderizer, 
        ContentLocator $contentLocator,
        IOInterface $io)
    {
        $this->configuration = $configuration;
        $this->converter = $converter;
        $this->renderizer = $renderizer;
        $this->contentLocator = $contentLocator;
        $this->io = $io;
    }
    
    /**
     * Get repository configuration
     * 
     * @return Yosymfony\Silex\ConfigServiceProvider\ConfigRepository
     */
    public function getConfigRepository()
    {
        return $this->configuration->getRepository();
    }
    
    /**
     * Get the template TemplateManager
     * 
     * @return Yosymfony\Spress\Plugin\Api\TemplateManager
     */
    public function getTemplateManager()
    {
        return new TemplateManager($this->renderizer);
    }
    
    /**
     * Access to IO API.
     * 
     * @return Yosymfony\Spress\IO\IOInterface
     */
    public function getIO()
    {
        return $this->io;
    }
    
    /**
     * Add new converter
     * 
     * @param ConverterInterface $converter
     */
    public function addConverter(ConverterInterface $converter)
    {
        $this->converter->addConverter($converter);
    }
    
    /**
     * Add a new Twig function
     * 
     * @param string $name Name of function
     * @param callable $function Function implementation
     * @param array $options
     */
    public function addTwigFunction($name, callable $function, array $options = [])
    {
        $this->renderizer->addTwigFunction($name, $function, $options);
    }
    
    /**
     * Add a new Twig filter
     * 
     * @param string $name Name of filter
     * @param callable $filter Filter implementation
     * @param array $options
     */
    public function addTwigFilter($name, callable $filter, array $options = [])
    {
        $this->renderizer->addTwigFilter($name, $filter, $options);
    }
    
    /**
     * Add a new Twig test
     * 
     * @param string $name Name of test
     * @param callable $test Test implementation
     * @param array $options
     */
    public function addTwigTest($name, callable $test, array $options = [])
    {
        $this->renderizer->addTwigTest($name, $test, $options);
    }
    
    public function getSourceDir()
    {
        return $this->contentLocator->getSourceDir();
    }
    
    public function getPostsDir()
    {
        return $this->contentLocator->getPostsDir();
    }
    
    public function getDestinationDir()
    {
        return $this->contentLocator->getDestinationDir();
    }
    
    
    /**
     * Get the absolute paths of includes directory
     * 
     * @return string
     */
    public function getIncludesDir()
    {
        return $this->contentLocator->getIncludesDir();
    }
    
    /**
     * Get the absolute paths of layouts directory
     * 
     * @return string
     */
    public function getLayoutsDir()
    {
        return $this->contentLocator->getLayoutsDir();
    }
}