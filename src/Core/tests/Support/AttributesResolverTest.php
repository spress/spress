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

use Yosymfony\Spress\Core\Support\AttributesResolver;

class AttributesResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDefault()
    {
        $a = new AttributesResolver();
        $a->setDefault('name', 'Yo! Symfony')
            ->setDefault('title', 'Hi', 'string')
            ->setDefault('host', 'localhost', 'string', false, true)
            ->setDefault('port', 4000, 'int', false, false)
            ->setDefault('path', '', 'string', true)
            ->setValidator('port', function ($value) {
                return $value > 0 && $value <= 65535;
            });

        $result = $a->resolve([
            'name' => 'Yo! Symfony 2',
            'path' => '/var/www',
        ]);

        $this->assertEquals(5, $a->count());
        $this->assertTrue(is_array($result));
        $this->assertEquals('Yo! Symfony 2', $result['name']);
        $this->assertEquals('Hi', $result['title']);
        $this->assertEquals('localhost', $result['host']);
        $this->assertEquals(4000, $result['port']);
        $this->assertEquals('/var/www', $result['path']);

        $a->remove('port');

        $this->assertEquals(4, $a->count());
        $this->assertFalse($a->hasdefault('port'));

        $a->remove(['path', 'host']);

        $this->assertEquals(2, $a->count());
        $this->assertFalse($a->hasdefault('path'));
        $this->assertFalse($a->hasdefault('host'));

        $a->clear();

        $this->assertEquals(0, $a->count());
    }

    public function testNullable()
    {
        $a = new AttributesResolver();
        $a->setDefault('port', 4000, 'int', true, true);

        $result = $a->resolve([
            'port' => null,
        ]);

        $this->assertNull($result['port']);
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\AttributeValueException
     */
    public function testBadType()
    {
        $a = new AttributesResolver();
        $a->setDefault('port', 4000, 'int', false, false);

        $result = $a->resolve([
            'port' => 'The port',
        ]);
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\MissingAttributeException
     */
    public function testRequired()
    {
        $a = new AttributesResolver();
        $a->setDefault('port', 4000, 'int', true)
            ->setDefault('host', 'localhost', 'string');

        $result = $a->resolve([
            'host' => '127.0.0.1',
        ]);
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\AttributeValueException
     */
    public function testNotNullable()
    {
        $a = new AttributesResolver();
        $a->setDefault('port', 4000, 'int', true, false);

        $result = $a->resolve([
            'port' => null,
        ]);
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\Exception\AttributeValueException
     */
    public function testInvalidValue()
    {
        $a = new AttributesResolver();
        $a->setDefault('port', 4000, 'int', false, false)
            ->setValidator('port', function ($value) {
                return $value > 0 && $value <= 65535;
            });

        $result = $a->resolve([
            'port' => -1,
        ]);
    }
}
