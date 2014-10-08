<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\ContentManager;

/**
 * Items paginator
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Paginator
{
    private $itemPaginated = [];
    private $totalPages = 0;
    private $totalItems = 0;
    private $itemsPerPage = 0;
    private $currentPage = 0;
    private $currentIndexInPage = 0;
    private $pageChanged = true;
    
    /**
     * Constructor
     * 
     * @param array $items
     * @param int $itemsPerPage
     */
    public function __construct(array $items, $itemsPerPage)
    {      
        if(false === is_int($itemsPerPage) || $itemsPerPage < 0)
        {
            throw new \InvalidArgumentException('Item per page must be integer and great or equals than 0');
        }
        
        $this->itemsPerPage = $itemsPerPage;
        
        if($itemsPerPage > 0)
        {
            $this->totalItems = count($items);
            $this->itemPaginated = array_chunk($items, $itemsPerPage);
            $this->totalPages = count($this->itemPaginated);
        }
    }
    
    /**
     * Get number of pagination pages
     * 
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }
    
    /**
     * Get total number of items
     * 
     * @return int
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }
    
    /**
     * Get the number of items per page
     * 
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }
    
    /**
     * Get the current page (1...n)
     * 
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage + 1;
    }
    
    /**
     * Get the current item
     * 
     * @return mixed
     */
    public function getItem()
    {
        $result = null;
        
        if(isset($this->itemPaginated[$this->currentPage][$this->currentIndexInPage]))
        {
            $result = $this->itemPaginated[$this->currentPage][$this->currentIndexInPage];
        }
        
        return $result;
    }
    
    /**
     * Get items containt in the current page
     * 
     * @return array Or null if page not exist
     */
    public function getItemsCurrentPage()
    {
        $result = null;
        
        if(isset($this->itemPaginated[$this->currentPage]))
        {
            $result = $this->itemPaginated[$this->currentPage];
        }
        
        return $result;
    }
    
    /**
     * Get the next page number
     * 
     * @return int Or false if no next page exists
     */
    public function getNextPage()
    {
        $next = $this->getCurrentPage() + 1;
        return $next <= $this->getTotalPages() ? $next: false;
    }
    
    /**
     * @return int Or false if no previous paga exists
     */
    public function getPreviousPage()
    {
        $previous = $this->getCurrentPage() - 1;
        return $previous > 0 ? $previous : false;
    }
    
    /**
     * Page changed is true if the current item is the first element of a page
     * 
     * @return bool
     */
    public function pageChanged()
    {
        return $this->pageChanged;
    }
    
    /**
     * Go to the next item
     * 
     * @return bool false if the last item of the last page
     */
    public function nextItem()
    {
        $this->currentIndexInPage++;
        $this->pageChanged = false;
        
        if(!isset($this->itemPaginated[$this->currentPage][$this->currentIndexInPage]))
        {
            $this->currentIndexInPage = 0;
            $this->currentPage++;
            $this->pageChanged = true;
            
            if(!isset($this->itemPaginated[$this->currentPage]))
            {
                $this->currentPage--;
                $this->pageChanged = false;
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Strat from scratch
     */
    public function start()
    {
        $this->currentPage = 0;
        $this->currentIndexInPage = 0;
        $this->pageChanged = true;
    }
}
