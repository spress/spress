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

use Yosymfony\Spress\Core\Exception\AttributeValueException;

/**
 * Twig renderizer
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class TwigRenderizer implements RenderizerInterface
{
    protected $twig;
    protected $arrayLoader;

    public function __construct(\Twig_Environment $twig, \Twig_Loader_Array $arrayLoader)
    {
        $this->twig = $twig;
        $this->arrayLoader = $arrayLoader;
    }

    public function addLayout($name, $content, array $attributes = [])
    {
        $fullname = $this->getLayoutNameWithNamespace($name);

        $this->arrayLoader->setTemplate($fullname, $content);
    }

    public function addInclude($name, $content, array $attributes = [])
    {
        $this->arrayLoader->setTemplate($name, $content);
    }

    public function renderBlocks($name, $content, array $attributes)
    {
        $this->arrayLoader->setTemplate('@dynamic/content', $content);

        return $this->twig->render('@dynamic/content', $attributes);
    }

    /**
     * @inheritDoc
     *
     * @throws \Yosymfony\Spress\Core\Exception\AttributeValueException if "layout" attribute has an invalid value.
     */
    public function renderPage($name, $content, array $attributes)
    {
        
    }

    protected function getLayoutAttribute(array $attributes, $name)
    {
        if (isset($attributes['layout']) === false) {
            return false;
        }

        if (is_string($attributes['layout']) === false) {
            throw new AttributeValueException('Invalid value. Expected string.', 'layout', $name);
        }

        if (strlen($attributes['layout']) == 0) {
            throw new AttributeValueException('Invalid value. Expected a non-empty string.', 'layout', $name);
        }

        return $this->getLayoutNameWithNamespace($attributes['layout']);
    }

    protected function getLayoutNameWithNamespace($name)
    {
        return '@layout/'.$name;
    }
}
