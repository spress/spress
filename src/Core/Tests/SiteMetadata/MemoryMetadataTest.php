<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Tests\SiteMetadata;

use PHPUnit\Framework\TestCase;
use Yosymfony\Spress\Core\SiteMetadata\MemoryMetadata;

class MemoryMetadataTest extends TestCase
{
    protected $memoryMetadata;

    public function setUp()
    {
        $this->memoryMetadata = new MemoryMetadata();
    }

    public function testGetMustReturnDefaultValueWhenSectionDoesNotExist()
    {
        $this->assertEquals('default', $this->memoryMetadata->get('invented', 'name', 'default'));
    }

    public function testGetMustReturnDefaultValueWhenKeyDoesNotExistInASection()
    {
        $this->memoryMetadata->set('acme', 'name', 'spress');

        $this->assertEquals('default', $this->memoryMetadata->get('acme', 'invented', 'default'));
    }

    public function testGetMustReturnKeyValueWhenSectionAndKeyExist()
    {
        $this->memoryMetadata->set('acme', 'name', 'spress');

        $this->assertEquals('spress', $this->memoryMetadata->get('acme', 'name'));
    }

    public function testSetMustSetTheValueAssociatedToKeyInASection()
    {
        $this->memoryMetadata->set('acme', 'name', 'spress');

        $this->assertEquals('spress', $this->memoryMetadata->get('acme', 'name'));
    }

    public function testSetSameKeyInDifferentSectionsIsAllows()
    {
        $this->memoryMetadata->set('acme', 'name', 'spress');
        $this->memoryMetadata->set('acme-x', 'name', 'spress-x');

        $this->assertEquals('spress', $this->memoryMetadata->get('acme', 'name'));
        $this->assertEquals('spress-x', $this->memoryMetadata->get('acme-x', 'name'));
    }

    public function testRemoveMustRemoveAKeyInASectionPreviouslyDefined()
    {
        $this->memoryMetadata->set('acme', 'name', 'spress');

        $this->memoryMetadata->remove('acme', 'name');

        $this->assertNull($this->memoryMetadata->get('acme', 'name'));
    }

    public function testRemoveMustRemoveAllKeysOfASectionWhenKeyArgumentIsNull()
    {
        $this->memoryMetadata->set('acme', 'name', 'spress');

        $this->memoryMetadata->remove('acme');

        $this->assertNull($this->memoryMetadata->get('acme', 'name'));
    }

    public function testClearMustRemoveAllSections()
    {
        $this->memoryMetadata->set('acme', 'name', 'spress');

        $this->memoryMetadata->clear();

        $this->assertNull($this->memoryMetadata->get('acme', 'name'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The value or default value must be a valid type: string, integer, float, boolean or array
     */
    public function testSetMustFailWhenValueIsAnObject()
    {
        $this->memoryMetadata->set('acme', 'date', new \DateTime());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The value or default value must be a valid type: string, integer, float, boolean or array
     */
    public function testGetMustFailWhenDefaultValueIsAnObject()
    {
        $this->memoryMetadata->get('acme', 'date', new \DateTime());
    }
}
