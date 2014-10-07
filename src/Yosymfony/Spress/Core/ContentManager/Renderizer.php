<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager;

use Yosymfony\Spress\Core\TwigFactory;
use Yosymfony\Spress\Core\Configuration;
use Yosymfony\Spress\Core\ContentLocator\ContentLocator;
use Yosymfony\Spress\Core\Exception\FrontmatterValueException;
use Yosymfony\Spress\Core\ContentManager\ContentItemInterface;

/**
 * Content renderizer
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Renderizer
{
    private $twig;
    private $contentLocator;
    private $configuration;
    private $layoutNamespace = 'layout';

    private $layoutItems;

    /**
     * Constructor
     *
     * @param TwigFactory $twigFactory
     * @param ContentLocator $contentLocator
     * @param Configuration $config
     */
    public function __construct(ContentLocator $contentLocator, Configuration $configuration)
    {
        $this->contentLocator = $contentLocator;
        $this->configuration = $configuration;
        $this->layoutItems = [];
        
        $this->buildTwig($this->layoutItems);
    }
    
    /**
     * initialize the Renderizer
     */
    public function initialize()
    {
        $this->layoutItems = $this->contentLocator->getLayouts();
        $this->buildTwig($this->layoutItems);
    }

    /**
     * Render the content of a item
     *
     * @param ContentItemInterface $item
     * @param array $payload
     */
    public function renderItem(ContentItemInterface $item, array $payload = [])
    {
        $content = $item->getPostConverterContent();
        $rendered = $this->renderString($content, $payload);
        $item->setPreLayoutContent($rendered);

        $layoutName = $this->getItemLayoutName($item);

        if($layoutName)
        {
            $payload['page']['content'] = $rendered;
            $layoutNameWithExt = $this->getFullLayoutName($layoutName);

            $rendered = $this->renderString($this->getTwigEntryPoint($layoutNameWithExt), $payload);
        }

        $item->setPostLayoutContent($rendered);
    }

    /**
     * Render string value
     *
     * @param string $value
     * @param array payload
     *
     * @return string
     */
    public function renderString($value, array $payload = [])
    {
        return $this->twig->render($value, $payload);
    }

    /**
     * Exists layout? e.g default
     *
     * @param string $name
     *
     * @return bool
     */
    public function existsLayout($name)
    {
        if($this->getFullLayoutName($name))
        {
            return true;
        }
        
        return false;
    }

    /**
     * Get full layout name with extension e.g. default.html
     *
     * @param string $name
     *
     * @return string | bool
     */
    public function getFullLayoutName($name)
    {
        foreach($this->configuration->getRepository()->get('layout_ext') as $ext)
        {
            if(isset($this->layoutItems[$name . '.' . $ext]))
            {
                return $name . '.' . $ext;
            }
        }

        return false;
    }

    /**
     * Add a new Twig filter
     *
     * @see http://twig.sensiolabs.org/doc/advanced.html#filters Twig documentation.
     *
     * @param string $name Name of filter
     * @param callable $filter Filter implementation
     * @param array $options
     */
    public function addTwigFilter($name, callable $filter, array $options = [])
    {
        $twigFilter = new \Twig_SimpleFilter($name, $filter, $options);
        $this->twig->addFilter($twigFilter);
    }

    /**
     * Add a new Twig function
     *
     * @see http://twig.sensiolabs.org/doc/advanced.html#functions Twig documentation.
     *
     * @param string $name Name of filter
     * @param callable $function Filter implementation
     * @param array $options
     */
    public function addTwigFunction($name, callable $function, array $options = [])
    {
        $twigfunction = new \Twig_SimpleFunction($name, $function, $options);
        $this->twig->addFunction($twigfunction);
    }

    /**
     * Add a new Twig test
     *
     * @see http://twig.sensiolabs.org/doc/advanced.html#tests Twig documentation.
     *
     * @param string $name Name of test
     * @param callable $function Test implementation
     * @param array $options
     */
    public function addTwigTest($name, callable $test, array $options = [])
    {
        $twigTest = new \Twig_SimpleTest($name, $test, $options);
        $this->twig->addTest($twigTest);
    }

    /**
     * @return string
     */
    private function getTwigEntryPoint($layoutName)
    {
        $result = '';
        $layout = $this->getLayoutNameWithNamespace($layoutName);

        if(strlen($layoutName) > 0)
        {
            $result = "{% extends \"$layout\" %}";
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getLayoutNameWithNamespace($name)
    {
        return sprintf('@%s/%s', $this->layoutNamespace, $name);
    }

    /**
     * @param ContentItemInterface $item
     *
     * @return string
     */
    private function getItemLayoutName(ContentItemInterface $item)
    {
        $layoutName = $item->getFrontmatter()->getFrontmatter()->get('layout');

        if($layoutName)
        {
            if(false == is_string($layoutName))
            {
                throw new FrontmatterValueException(
                    sprintf('Invalid value.', $layoutName),
                    'layout',
                    $item->getFileItem()->getFileName()
                );
            }

            if(false == $this->existsLayout($layoutName))
            {
                throw new FrontmatterValueException(
                    sprintf('Layout "%s" not found.', $layoutName),
                    'layout',
                    $item->getFileItem()->getFileName()
                );
            }

            return $layoutName;
        }
        else
        {
            return '';
        }
    }

    private function processLayouts(array $layouts)
    {
        $result = [];

        foreach($layouts as $layout)
        {
            $pageItem = new PageItem($layout, $this->configuration);

            $layoutName = $this->getItemLayoutName($pageItem);
            $content = $pageItem->getPreConverterContent();

            if($layoutName)
            {
                $layoutNameWithExt = $this->getFullLayoutName($layoutName);
                $content = $this->getTwigEntryPoint($layoutNameWithExt) . $content;
            }

            $name = $this->getLayoutNameWithNamespace($layout->getRelativePathFilename());
            $result[$name] = $content;
        }

        return $result;
    }

    private function buildTwig(array $layouts)
    {
        $templates = $this->processLayouts($layouts);
        $includesDir = $this->contentLocator->getIncludesDir();
        $extraDirs = [];

        if($includesDir)
        {
            $extraDirs[] = $includesDir;
        }
        
        $twigFactory = new TwigFactory();
        $this->twig = $twigFactory
            ->withAutoescape(false)
            ->withCache(false)
            ->addLoaderFilesystem($extraDirs)
            ->addLoaderArray($templates)
            ->addLoaderString()
            ->create();
    }
}
