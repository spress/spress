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

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\ContentManager\Renderizer\TwigRenderizer;

class TwigRenderizerTest extends TestCase
{
    public function testRenderBlocksMustRenderATemplate()
    {
        $renderizer = $this->getRenderizer();
        $rendered = $renderizer->renderBlocks('index.html', 'Hi {{ name }}.', ['name' => 'Yo! Symfony']);

        $this->assertEquals('Hi Yo! Symfony.', $rendered);
    }

    public function testRenderBlocksMustRenderATemplateWithIncludes()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addInclude('message', 'This is a message.');
        $rendered = $renderizer->renderBlocks('index.html', "What's it?: {% include 'message' %}", []);

        $this->assertEquals("What's it?: This is a message.", $rendered);
    }

    public function testRenderPageMustRenderAPageWithLayout()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addLayout('default', '<h1>Hi</h1>{% block content %}{{ page.content }}{% endblock %}');
        $rendered = $renderizer->renderPage('index.html', 'Yo! Symfony', 'default', []);

        $this->assertEquals('<h1>Hi</h1>Yo! Symfony', $rendered);
    }

    public function testRenderPageMustRenderAPageWithAHierachyOfLayouts()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addLayout('default', '<html></body>{% block page %}{{ page.content }}{% endblock %}</body></html>');
        $renderizer->addLayout('page', '{% block page %}<h1>Hi</h1>{{ page.content }}{% endblock %}', ['layout' => 'default']);
        $rendered = $renderizer->renderPage('index.html', 'Yo! Symfony', 'page', []);

        $this->assertEquals('<html></body><h1>Hi</h1>Yo! Symfony</body></html>', $rendered);
    }

    /**
     * @expectedException Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException
     * @expectedExceptionMessage Layout "madeUpLayout" not found in "index.html" at key "layout".
     */
    public function testRenderPageMustFailWhenLayoutNotFound()
    {
        $renderizer = $this->getRenderizer();
        $rendered = $renderizer->renderPage('index.html', 'Yo! Symfony', 'madeUpLayout', []);
    }

    private function getRenderizer()
    {
        $twigLoader = new \Twig_Loader_Array([]);
        $twig = new \Twig_Environment($twigLoader, ['autoescape' => false]);

        return new TwigRenderizer($twig, $twigLoader, ['twig']);
    }
}
