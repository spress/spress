<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Plugin\Event;

use Symfony\Component\EventDispatcher\Event;

class FinishEvent extends Event
{
    protected $result;
    
    public function __construct(array $result)
    {
        $this->result = $result;
    }
    
    /**
     * Get restult data
     * 
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }
}
