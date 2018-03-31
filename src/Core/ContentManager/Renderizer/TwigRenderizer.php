<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Renderizer;

use Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException;
use Yosymfony\Spress\Core\ContentManager\Renderizer\Exception\RenderException;

/**
 * Renderizer for Twig template engine.
 *
 * @api
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class TwigRenderizer implements RenderizerInterface
{
    protected $twig;
    protected $arrayLoader;
    protected $layouts = [];
    protected $isLayoutsProcessed;

    /**
     * Construct.
     *
     * @param Twig_Environment  $twig            The Twig instance
     * @param Twig_Loader_Array $arrayLoader     The loader
     */
    public function __construct(\Twig_Environment $twig, \Twig_Loader_Array $arrayLoader)
    {
        $this->twig = $twig;
        $this->arrayLoader = $arrayLoader;
        $this->isLayoutsProcessed = false;
    }

    /**
     * Adds a new layout.
     *
     * @param string $id         The identifier of the layout. e.g: default
     * @param string $content    The content of the layout
     * @param array  $attributes The attributes of the layout.
     *                           "layout" attribute has a special meaning.
     */
    public function addLayout($id, $content, array $attributes = [])
    {
        $namespaceLayoutId = $this->getLayoutNameWithNamespace($id);
        $this->layouts[$namespaceLayoutId] = [$id, $content, $attributes];
    }

    /**
     * {@inheritdoc}
     */
    public function addInclude($id, $content, array $attributes = [])
    {
        $this->arrayLoader->setTemplate($id, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->twig->clearCacheFiles();
        $this->layouts = [];
        $this->isLayoutsProcessed = false;
    }

    /**
     * Renders the content blocks (layout NOT included).
     *
     * @param string $id         The identifier of the item.
     * @param string $content    The content.
     * @param array  $attributes The attributes for using inside the content.
     *
     * @return string The block rendered.
     *
     * @throws RenderException If an error occurred during rendering the content.
     */
    public function renderBlocks($id, $content, array $attributes)
    {
        try {
            $this->arrayLoader->setTemplate('@dynamic/content', $content);

            return $this->twig->render('@dynamic/content', $attributes);
        } catch (\Twig_Error_Syntax $e) {
            throw new RenderException('Error during lexing or parsing a template.', $id, $e);
        }
    }

    /**
     * Renders a page completely (layout included). The value of param $content
     * will be placed at "page.content" attribute.
     *
     * @param string $id             The identifier of the item.
     * @param string $content        The page content.
     * @param string $layoutName     The layout name.
     * @param array  $siteAttributes The attributes for using inside the content.
     *                               "layout" attribute has a special meaning.
     *
     * @return string The page rendered
     *
     * @throws AttributeValueException   If "layout" attribute has an invalid value
     *                                   or layout not found.
     * @throws RenderException If an error occurred during rendering the content.
     */
    public function renderPage($id, $content, $layoutName, array $siteAttributes)
    {
        if ($this->isLayoutsProcessed === false) {
            $this->processLayouts();
        }

        if ($layoutName) {
            $namespaceLayoutId = $this->getLayoutNameWithNamespace($layoutName);

            if (isset($this->layouts[$namespaceLayoutId]) === false) {
                throw new AttributeValueException(sprintf('Layout "%s" not found.', $layoutName), 'layout', $id);
            }

            if (isset($siteAttributes['page']) === false) {
                $siteAttributes['page'] = [];
            }

            $siteAttributes['page']['content'] = $content;
            $content = sprintf('{%% extends "%s" %%}', $namespaceLayoutId);
        }

        return $this->renderBlocks($id, $content, $siteAttributes);
    }

    /**
     * Adds a new Twig filter.
     *
     * @see http://twig.sensiolabs.org/doc/advanced.html#filters Twig documentation
     *
     * @param string   $name    Name of filter
     * @param callable $filter  Filter implementation
     * @param array    $options
     */
    public function addTwigFilter($name, callable $filter, array $options = [])
    {
        $twigFilter = new \Twig_SimpleFilter($name, $filter, $options);

        $this->twig->addFilter($twigFilter);
    }

    /**
     * Adds a new Twig function.
     *
     * @see http://twig.sensiolabs.org/doc/advanced.html#functions Twig documentation
     *
     * @param string   $name     Name of filter
     * @param callable $function Filter implementation
     * @param array    $options
     */
    public function addTwigFunction($name, callable $function, array $options = [])
    {
        $twigfunction = new \Twig_SimpleFunction($name, $function, $options);

        $this->twig->addFunction($twigfunction);
    }

    /**
     * Adds a new Twig test.
     *
     * @see http://twig.sensiolabs.org/doc/advanced.html#tests Twig documentation
     *
     * @param string   $name     Name of test
     * @param callable $function Test implementation
     * @param array    $options
     */
    public function addTwigTest($name, callable $test, array $options = [])
    {
        $twigTest = new \Twig_SimpleTest($name, $test, $options);

        $this->twig->addTest($twigTest);
    }

    /**
     * Adds a new Twig tag.
     *
     * @see http://twig.sensiolabs.org/doc/advanced.html#tags Twig documentation
     *
     * @param Twig_TokenParser $tokenParser Twig Token parser
     */
    public function addTwigTag(\Twig_TokenParser $tokenParser)
    {
        $this->twig->addTokenParser($tokenParser);
    }

    /**
     * Returns the value of layout attribute.
     *
     * @param array  $attributes  List of attributes
     * @param string $contentName The identifier of the content
     *
     * @return string
     */
    protected function getLayoutAttributeWithNamespace(array $attributes, $contentName)
    {
        if (isset($attributes['layout']) === false) {
            return '';
        }

        if (is_string($attributes['layout']) === false) {
            throw new AttributeValueException('Invalid value. Expected string.', 'layout', $contentName);
        }

        if (strlen($attributes['layout']) == 0) {
            throw new AttributeValueException('Invalid value. Expected a non-empty string.', 'layout', $contentName);
        }

        return $this->getLayoutNameWithNamespace($attributes['layout']);
    }

    /**
     * Returns the layout name with the namespace prefix: "@layout/".
     *
     * @param string $name The layout name.
     *
     * @return string The layout name with namespace.
     */
    protected function getLayoutNameWithNamespace($name)
    {
        return '@layout/'.$name;
    }

    protected function processLayouts()
    {
        foreach ($this->layouts as $namespaceLayoutId => list($id, $content, $attributes)) {
            $parentNamespaceLayoutId = $this->getLayoutAttributeWithNamespace($attributes, $id);

            if ($parentNamespaceLayoutId !== '') {
                $content = sprintf('{%% extends "%s" %%}%s', $parentNamespaceLayoutId, $content);
            }

            $this->arrayLoader->setTemplate($namespaceLayoutId, $content);
        }

        $this->isLayoutsProcessed = true;
    }
}
