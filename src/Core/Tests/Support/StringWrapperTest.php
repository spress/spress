<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\Support;

use Yosymfony\Spress\Core\Support\StringWrapper;

class StringWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testSlug()
    {
        $str = new StringWrapper('Welcome to Spress');

        $this->assertEquals('welcome-to-spress', $str->slug());
        $this->assertEquals('bienvenido-a-espana', $str->setString('Bienvenido a España')->slug());
        $this->assertEquals('version-2-0-0', $str->setString('version 2.0.0')->slug());
        $this->assertEquals('hello-spress', $str->setString('hello  spress')->slug());
        $this->assertEquals('hello-spress', $str->setString('-hello-spress-')->slug());
        $this->assertEquals('12-cheese', $str->setString('1\2 cheese')->slug());
        $this->assertEquals('2-step', $str->setString('.,;{}+¨¿?=()/&%$·#@|!ºª2 step     ^[]')->slug());
    }

    public function testStartWith()
    {
        $str = new StringWrapper('Welcome to Spress');

        $this->assertTrue($str->startWith('Wel'));
        $this->assertFalse($str->startWith('Well'));
    }

    public function testEndWith()
    {
        $str = new StringWrapper('Welcome to Spress');

        $this->assertTrue($str->endWith('press'));
        $this->assertFalse($str->endWith('to'));
    }

    public function testDeletePrefix()
    {
        $str = new StringWrapper('Welcome to Spress');

        $this->assertEquals('to Spress', $str->deletePrefix('Welcome '));
        $this->assertEquals('Welcome to Spress', $str->deletePrefix('Hi'));
        $this->assertEquals('Welcome to Spress', $str->deletePrefix(''));
    }

    public function testDeleteSufix()
    {
        $str = new StringWrapper('Welcome to Spress');

        $this->assertEquals('Welcome to', $str->deleteSufix(' Spress'));
        $this->assertEquals('Welcome to Spress', $str->deleteSufix('Hi'));
        $this->assertEquals('Welcome to Spress', $str->deleteSufix(''));
    }

    public function testToAscii()
    {
        $str = new StringWrapper('camión');

        $this->assertEquals('camion', $str->toAscii());

        $str = new StringWrapper('españa');
        $this->assertEquals('espana', $str->toAscii());
    }
}
