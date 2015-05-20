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

use Yosymfony\Spress\Core\Support\ArrayWrapper;

class ArrayWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $a = new ArrayWrapper();
        $a->add('name', 'Yo! Symfony');
        $a->add('name', 'Yo! Symfony 2');
        $a->add('title', 'Hi');

        $this->assertEquals('Yo! Symfony', $a->get('name'));
        $this->assertEquals('Hi', $a->get('title'));
    }

    public function testGet()
    {
        $a = new ArrayWrapper([
            'site' => [
                'name' => 'Yo! Symfony',
            ],
        ]);

        $this->assertTrue(is_array($a->get('site')));
        $this->assertEquals('Yo! Symfony', $a->get('site.name'));
        $this->assertEquals('Default value', $a->get('site.not-exists', 'Default value'));
        $this->assertEquals('Default value', $a->get('', 'Default value'));
        $this->assertEquals('Default value', $a->get(null, 'Default value'));
        $this->assertNull($a->get(''));
        $this->assertNull($a->get(null));
    }

    public function testHas()
    {
        $a = new ArrayWrapper();
        $a->set('site.name', 'Yo! Symfony');

        $this->assertTrue($a->has('site.name'));
        $this->assertFalse($a->has('site.notExists'));
    }

    public function testInitialize()
    {
        $a = new ArrayWrapper();
        $a->setArray([
            'name' => 'Yo! Symfony',
        ]);

        $this->assertEquals('Yo! Symfony', $a->get('name'));
        $this->assertCount(1, $a->getArray());
    }

    public function testSet()
    {
        $a = new ArrayWrapper();
        $a->set('site.name', 'Yo! Symfony');

        $this->assertEquals('Yo! Symfony', $a->get('site.name'));
    }

    public function testWhere()
    {
        $data = [];
        $a = new ArrayWrapper(['value1' => 1, 'value2' => 2]);

        $filtered = $a->where(function ($key, $value) {
            return $value > 1;
        });

        $this->assertCount(1, $filtered);
    }
}
