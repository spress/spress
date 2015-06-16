<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\tests\Support;

use Yosymfony\Spress\Core\Support\StringWrapper;

class StringWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testSlug()
    {
        $str = new StringWrapper('Welcome to Spress');

        $this->assertEquals('welcome-to-spress', $str->slug());
        $this->assertEquals('bienvenido-a-espana', $str->setString('Bienvenido a España')->slug());
        $this->assertEquals('hello-spress', $str->setString('hello  spress')->slug());
        $this->assertEquals('hello-spress', $str->setString('-hello-spress-')->slug());
        $this->assertEquals('12-cheese', $str->setString('1\2 cheese')->slug());
        $this->assertEquals('2-step', $str->setString('.,;{}+¨¿?=()/&%$·#@|!ºª2 step     ^[]')->slug());
    }

    public function testToAscii()
    {
        $str = new StringWrapper('camión');

        $this->assertEquals('camion', $str->toAscii());

        $str = new StringWrapper('españa');
        $this->assertEquals('espana', $str->toAscii());
    }
}
