<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests;

use Yosymfony\Spress\Core\TwigFactory;

class TwigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = new TwigFactory();
        $twig = $factory->withAutoescape(false)
            ->withCache(false)
            ->withDebug(false)
            ->addLoaderString()
            ->addLoaderArray([
                'index.html' => 'Hi {{name}}',
            ])
            ->create();

        $this->assertInstanceOf('Twig_Environment', $twig);
    }

    public function testCreateLoaderFilesystem()
    {
        $factory = new TwigFactory();
        $twig = $factory->withAutoescape(false)
            ->withCache(false)
            ->withDebug(false)
            ->addLoaderString()
            ->addLoaderFilesystem(__DIR__)
            ->create(__dir__);

        $this->assertInstanceOf('Twig_Environment', $twig);
    }

    public function testCreateLoaderFilesystemNamespace()
    {
        $factory = new TwigFactory();
        $twig = $factory->withAutoescape(false)
            ->withCache(false)
            ->withDebug(false)
            ->addLoaderString()
            ->addLoaderFilesystem(array('myNamespace' => __DIR__))
            ->create();

        $this->assertInstanceOf('Twig_Environment', $twig);
    }
}
