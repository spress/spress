<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\ContentManager\Renderizer;

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
    }

    public function testRenderTemplateWithInclude()
    {
        $renderizer = $this->getRenderizer();
        $renderizer->addInclude('message', 'This is a message.');

        $rendered = $renderizer->renderBlocks('index.html', "What's it?: {% include 'message' %}", []);

        $this->assertEquals("What's it?: This is a message.", $rendered);
    }

    private function getRenderizer()
    {
        $twigLoader = new \Twig_Loader_Array([]);
        $twig = new \Twig_Environment($twigLoader, ['autoescape' => false]);

        return new TwigRenderizer($twig, $twigLoader);
    }
}
