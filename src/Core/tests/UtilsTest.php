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

use Yosymfony\Spress\Core\Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testSlugify()
    {
        $this->assertEquals('hola-espana-voy-en-camion', Utils::slugify('hola españa, voy en camión'));
        $this->assertEquals('2-step', Utils::slugify('.,;{}+¨¿?=()/&%$·#@|!ºª2 step     ^[]'));
        $this->assertEquals('1-2-cheese', Utils::slugify('1\2 cheese'));
        $this->assertEquals('garcon', Utils::slugify('garçon'));
        $this->assertEquals('hello-spress', Utils::slugify('hello-spress'));
        $this->assertEquals('hello-spress', Utils::slugify('-hello-spress-'));
    }
}
