<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\Tests\ContentManager;

use Yosymfony\Spress\Core\ContentManager\Paginator;

class PaginatorTest extends \PHPUnit_Framework_TestCase
{
    public function testPaginator()
    {
        $paginator = new Paginator(['a', 'b', 'c', 'd'], 2);
        
        $this->assertEquals(2, $paginator->getTotalPages());
        $this->assertEquals(4, $paginator->getTotalItems());
        $this->assertEquals(2, $paginator->getItemsPerPage());
        $this->assertEquals(1, $paginator->getCurrentPage());
        $this->assertEquals('a', $paginator->getItem());
        $this->assertCount(2, $paginator->getItemsCurrentPage());
        $this->assertTrue($paginator->pageChanged());
        $this->assertEquals(2, $paginator->getNextPage());
        $this->assertFalse($paginator->getPreviousPage());
        
        $existsMore = $paginator->nextItem();
        
        $this->assertTrue($existsMore);
        $this->assertEquals(1, $paginator->getCurrentPage());
        $this->assertEquals('b', $paginator->getItem());
        $this->assertFalse($paginator->pageChanged());
        $this->assertEquals(2, $paginator->getNextPage());
        $this->assertFalse($paginator->getPreviousPage());
        
        $existsMore = $paginator->nextItem();
        
        $this->assertTrue($existsMore);
        $this->assertCount(2, $paginator->getItemsCurrentPage());
        $this->assertEquals(2, $paginator->getCurrentPage());
        $this->assertEquals('c', $paginator->getItem());
        $this->assertTrue($paginator->pageChanged());
        $this->assertFalse($paginator->getNextPage());
        $this->assertEquals(1, $paginator->getPreviousPage());
        
        $existsMore = $paginator->nextItem();
        
        $this->assertTrue($existsMore);
        $this->assertEquals(2, $paginator->getCurrentPage());
        $this->assertEquals('d', $paginator->getItem());
        $this->assertFalse($paginator->pageChanged());
        $this->assertFalse($paginator->getNextPage());
        $this->assertEquals(1, $paginator->getPreviousPage());
        
        $existsMore = $paginator->nextItem();
        
        $this->assertFalse($existsMore);
        $this->assertEquals(2, $paginator->getCurrentPage());
        $this->assertFalse($paginator->getNextPage());
    }
    
    public function testPaginatorOdd()
    {
        $paginator = new Paginator(['a', 'b', 'c', 'd'], 3);
        
        $this->assertEquals(2, $paginator->getTotalPages());
        $this->assertEquals(4, $paginator->getTotalItems());
        $this->assertEquals(3, $paginator->getItemsPerPage());
        $this->assertEquals(1, $paginator->getCurrentPage());
        $this->assertEquals('a', $paginator->getItem());
        $this->assertCount(3, $paginator->getItemsCurrentPage());
        $this->assertTrue($paginator->pageChanged());
        $this->assertEquals(2, $paginator->getNextPage());
        
        $existsMore = $paginator->nextItem();
        
        $this->assertTrue($existsMore);
        $this->assertEquals(1, $paginator->getCurrentPage());
        $this->assertEquals('b', $paginator->getItem());
        $this->assertFalse($paginator->pageChanged());
        
        $existsMore = $paginator->nextItem();
        
        $this->assertTrue($existsMore);
        $this->assertEquals(1, $paginator->getCurrentPage());
        $this->assertEquals('c', $paginator->getItem());
        $this->assertFalse($paginator->pageChanged());
        
        $existsMore = $paginator->nextItem();
        
        $this->assertTrue($existsMore);
        $this->assertEquals(2, $paginator->getCurrentPage());
        $this->assertCount(1, $paginator->getItemsCurrentPage());
        $this->assertEquals('d', $paginator->getItem());
        $this->assertTrue($paginator->pageChanged());
        
        $existsMore = $paginator->nextItem();
        
        $this->assertFalse($existsMore);
    }
    
    public function testPaginatorZeroSize()
    {
        $paginator = new Paginator(['a', 'b', 'c', 'd'], 0);
        
        $this->assertEquals(0, $paginator->getTotalPages());
        $this->assertEquals(0, $paginator->getTotalItems());
        $this->assertEquals(0, $paginator->getItemsPerPage());
        $this->assertEquals(1, $paginator->getCurrentPage());
        $this->assertNull($paginator->getItemsCurrentPage());
        $this->assertNull($paginator->getItem());
        $this->assertTrue($paginator->pageChanged());
        $this->assertFalse($paginator->nextItem());
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPaginatorNegativeSize()
    {
        $paginator = new Paginator(['a', 'b', 'c', 'd'], -1);
    }
}