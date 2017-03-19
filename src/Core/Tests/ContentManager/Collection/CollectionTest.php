<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\ContentManager\Collection;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\ContentManager\Collection\Collection;

class CollectionTest extends TestCase
{
    public function testCollection()
    {
        $collection = new Collection('events', '_events', ['output' => true]);

        $this->assertEquals('events', $collection->getName());
        $this->assertEquals('_events', $collection->getPath());
        $this->assertTrue(is_array($collection->getAttributes()));
        $this->assertArrayHasKey('output', $collection->getAttributes());
    }
}
