<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\ContentManager\Renderizer;

use Yosymfony\Spress\Core\ContentManager\Renderizer\TwigRenderizer;

class TwigRenderizerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \Twig_Autoloader::register();
    }

    public function testRenderTemplate()
    {
        $renderizer = $this->getRenderizer();

        $rendered = $renderizer->renderBlocks('index.html', 'Hi {{ name }}.', ['name' => 'Yo! Symfony']);

        $this->assertEquals('Hi Yo! Symfony.', $rendered);

        $rendered = $renderizer->renderBlocks('2015/05/04/hello.html', 'This is a new post called "{{ name }}".', ['name' => 'hello']);

        $this->assertEquals('This is a new post called "hello".', $rendered);

        $renderizer->clear();
    }

    public function testRenderTemplateWithInclude()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addInclude('message', 'This is a message.');

        $rendered = $renderizer->renderBlocks('index.html', "What's it?: {% include 'message' %}", []);

        $this->assertEquals("What's it?: This is a message.", $rendered);
    }

    public function testRenderTemplateWithLayout()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addLayout('default.twig', '<h1>Hi</h1>{% block content %}{{ page.content }}{% endblock %}');
        $rendered = $renderizer->renderPage('index.html', 'Yo! Symfony', 'default', []);

        $this->assertEquals('<h1>Hi</h1>Yo! Symfony', $rendered);
    }

    public function testRenderTemplateWithStackOfLayouts()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addLayout('default', '<html></body>{% block page %}{{ page.content }}{% endblock %}</body></html>');
        $renderizer->addLayout('page', '{% block page %}<h1>Hi</h1>{{ page.content }}{% endblock %}', ['layout' => 'default']);
        $rendered = $renderizer->renderPage('index.html', 'Yo! Symfony', 'page', []);

        $this->assertEquals('<html></body><h1>Hi</h1>Yo! Symfony</body></html>', $rendered);
    }

    public function testAddTwigFunction()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addTwigFunction('fuTest1', function ($param) {
            return $param;
        });
    }

    public function testAddTwigFunctionWithOptions()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addTwigFunction('fuTest2', function (\Twig_Environment $env, $context, $param) {
            return $param;
        }, ['needs_context' => true, 'needs_environment' => true]);
    }

    public function testAddTwigFilter()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addTwigFilter('fTest', function ($param) {
            return $param;
        });
    }

    public function testAddTwigFilterWithOptions()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addTwigFilter('fTest', function (\Twig_Environment $env, $context, $param) {
            return $param;
        }, ['needs_context' => true, 'needs_environment' => true]);
    }

    public function testAddTwigTest()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addTwigFilter('tTest', function ($param) {
            return true;
        });
    }

    public function testAddTwigTag()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addTwigTag(new TwigTag);
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException
     */
    public function testRenderTemplateLayoutNotFound()
    {
        $renderizer = $this->getRenderizer();
        $rendered = $renderizer->renderPage('index.html', 'Yo! Symfony', 'default', []);

        $this->assertEquals('<h1>Hi</h1>Yo! Symfony', $rendered);
    }

    private function getRenderizer()
    {
        $twigLoader = new \Twig_Loader_Array([]);
        $twig = new \Twig_Environment($twigLoader, ['autoescape' => false]);

        return new TwigRenderizer($twig, $twigLoader, ['twig']);
    }
}

class TwigTag extends \Twig_TokenParser
{
    public function parse(\Twig_Token $token)
    {
        return new \Twig_Node_Text('test', $token->getLine());
    }

    public function getTag()
    {
        return 'test';
    }
}
