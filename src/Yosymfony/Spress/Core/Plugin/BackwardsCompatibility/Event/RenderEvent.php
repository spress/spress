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

use Yosymfony\Spress\Core\ContentManager\Renderizer;
use Yosymfony\Spress\Core\ContentManager\ContentItemInterface;

class RenderEvent extends ContentEvent
{
    protected $render;
    protected $payload;
    
    public function __construct(Renderizer $render, array $payload, ContentItemInterface $item, $isPost = false)
    {
        parent::__construct($item, $isPost);
        
        $this->render = $render;
        $this->payload = $payload;
    }
    
    /**
     * Render content with Twig template engine
     * 
     * @param string $content
     * @param array $payload Data available in the template
     * 
     * @return string
     */
    public function render($content, array $payload)
    {
        return $this->render->renderString($content, $payload);
    }
    
    /**
     * Get the model data available in templates
     * 
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }
    
    /**
     * Set a new model data available in templates
     * 
     * @param array $payload Model
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
    }
}