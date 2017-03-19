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

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\Support\AttributesResolver;

class AttributesResolverTest extends TestCase
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

    public function testNullableWithValidator()
    {
        $a = new AttributesResolver();
        $a->setDefault('layout', null, 'string', false, true)
            ->setValidator('layout', function ($value) {
                return strlen($value) > 0;
            });

        $result = $a->resolve([]);

        $this->assertNull($result['layout']);

        $result = $a->resolve(['layout' => 'default']);

        $this->assertEquals('default', $result['layout']);
    }

    /**
     * @expectedException \Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException
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
     * @expectedException \Yosymfony\Spress\Core\ContentManager\Exception\MissingAttributeException
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
     * @expectedException \Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException
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
     * @expectedException \Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException
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
