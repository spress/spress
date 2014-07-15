<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Command;

use Yosymfony\Spress\Core\Application;
use Yosymfony\Spress\Core\IO\IOInterface;

/**
 * Spress core wrapper with the options for SpressCLI
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SpressCLI extends Application
{
    /**
     * Constructor
     * 
     * @param IOInterface $io
     */
    public function __construct(IOInterface $io)
    {
        $spressPath = realpath(dirname(__FILE__) . '/../../../../');
        
        $options = [
            'spress.paths' => [
                'root'      => $spressPath,
                'config'    => $spressPath . '/app/config/',
                'templates' => $this->getTemplatesPath($spressPath),
            ],
            'spress.io' => $io,
        ];
        
        parent::__construct($options);
    }
    
    private function getTemplatesPath($spressPath)
    {
        if(file_exists($spressPath . '/app/templates/'))
        {
            return $spressPath . '/app/templates';
        }
        
        return realpath($spressPath . '/../spress-templates');
    }
}