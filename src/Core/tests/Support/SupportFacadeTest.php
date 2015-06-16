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

use Yosymfony\Spress\Core\Support\SupportFacade;

class SupportFacadeTest extends \PHPUnit_Framework_TestCase
{
    protected $support;

    public function setUp()
    {
        $this->support = new SupportFacade();
    }

    public function testGetArrayWrapper()
    {
        $this->assertInstanceOf('\Yosymfony\Spress\Core\Support\ArrayWrapper', $this->support->getArrayWrapper());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\Support\ArrayWrapper', $this->support->getArrayWrapper([]));
    }

    public function testGetStringWrapper()
    {
        $this->assertInstanceOf('\Yosymfony\Spress\Core\Support\StringWrapper', $this->support->getStringWrapper());
        $this->assertInstanceOf('\Yosymfony\Spress\Core\Support\StringWrapper', $this->support->getStringWrapper('Hi'));
    }

    public function testGetAttributeResolver()
    {
        $this->assertInstanceOf('\Yosymfony\Spress\Core\Support\AttributesResolver', $this->support->getAttributesResolver());
    }
}
