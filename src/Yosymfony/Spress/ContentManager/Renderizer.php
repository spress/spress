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

use Yosymfony\Spress\TwigFactory;
use Yosymfony\Spress\Configuration;
use Yosymfony\Spress\ContentLocator\ContentLocator;
use Yosymfony\Spress\Exception\FrontmatterValueException;
use Yosymfony\Spress\ContentManager\ContentItemInterface;

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
    public function __construct(TwigFactory $twigFactory, ContentLocator $contentLocator, Configuration $configuration)
    {
        $this->contentLocator = $contentLocator;
        $this->configuration = $configuration;
        $this->layoutItems = $this->contentLocator->getLayouts();

        $this->buildTwig($twigFactory, $this->layoutItems);
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

            $rendered = $this->renderString($this->getTwigEntryPoint($layoutName), $payload);
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
        $exists = false;

        if($this->getFullLayoutName($name)){
            $exists = true;
        }


        return $exists;
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
        $fullName = false;

        foreach($this->configuration->getRepository()->get('layout_ext') as $ext){
            if(false === $fullName){
                if(isset($this->layoutItems[$name . '.' . $ext])){
                    $fullName = $name . $ext;
                }
            }
        }

        return $fullName;
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
        $layout = $this->getLayoutName($layoutName);

        if(strlen($layoutName) > 0)
        {
            $result = "{% extends \"$layout\" %}";
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getLayoutName($name)
    {
        $fullName = $this->getFullLayoutName($name);
        return sprintf('@%s/%s', $this->layoutNamespace, $fullName);
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

            return $this->getFullLayoutName($layoutName);
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
                $content = $this->getTwigEntryPoint($layoutName) . $content;
            }

            $name = $this->getLayoutName($layout->getRelativePathFilename());
            $result[$name] = $content;
        }

        return $result;
    }

    private function buildTwig(TwigFactory $twigFactory, array $layouts)
    {
        $templates = $this->processLayouts($layouts);
        $includesDir = $this->contentLocator->getIncludesDir();
        $extraDirs = [];

        if($includesDir)
        {
            $extraDirs[] = $includesDir;
        }

        $this->twig = $twigFactory
            ->withAutoescape(false)
            ->withCache(false)
            ->addLoaderFilesystem($extraDirs)
            ->addLoaderArray($templates)
            ->addLoaderString()
            ->create();
    }
}
