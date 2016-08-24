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

use Yosymfony\Spress\Core\ContentManager\Collection\CollectionManagerBuilder;

class CollectionManagerBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildFromConfigArray()
    {
        $config = [
            'events' => [
                'output' => true,
                'title' => 'Events',
            ],
        ];

        $builder = new CollectionManagerBuilder();
        $cm = $builder->buildFromConfigArray($config);

        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Collection\CollectionManager', $cm);

        $collection = $cm->getCollectionItemCollection()->get('events');

        $this->assertEquals('events', $collection->getName());
        $this->assertEquals('events', $collection->getPath());
        $this->assertCount(2, $collection->getAttributes());
        $this->assertArrayHasKey('title', $collection->getAttributes());
        $this->assertArrayHasKey('output', $collection->getAttributes());
    }

    public function testBuildFromConfigArrayNoAttributes()
    {
        $config = [
            'events' => [],
        ];

        $builder = new CollectionManagerBuilder();
        $cm = $builder->buildFromConfigArray($config);

        $this->assertInstanceOf('\Yosymfony\Spress\Core\ContentManager\Collection\CollectionManager', $cm);

        $collection = $cm->getCollectionItemCollection()->get('events');

        $this->assertEquals('events', $collection->getName());
        $this->assertEquals('events', $collection->getPath());
        $this->assertCount(0, $collection->getAttributes());
    }

    public function testBuildFromConfigArrayWithPageCollection()
    {
        $config = [
            'pages' => [
                'layout' => 'default',
            ],
        ];

        $builder = new CollectionManagerBuilder();
        $cm = $builder->buildFromConfigArray($config);

        $collection = $cm->getCollectionItemCollection()->get('pages');

        $this->assertEquals('pages', $collection->getName());
        $this->assertEquals('', $collection->getPath());
        $this->assertCount(1, $collection->getAttributes());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Expected array at the collection: "events".
     */
    public function testBuildFromConfigArrayWithBadData()
    {
        $config = [
            'events' => true,
        ];

        $builder = new CollectionManagerBuilder();
        $builder->buildFromConfigArray($config);
    }
}
