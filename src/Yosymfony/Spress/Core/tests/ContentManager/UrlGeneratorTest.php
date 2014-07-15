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

use Yosymfony\Spress\Core\ContentManager\UrlGenerator;

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUrlTemplate()
    {
        $generator = new UrlGenerator();
        $template = '/:categories/:title/';
        $placeholders = [
            ':categories' => 'tech/news',
            ':title' => 'new-gadget',
        ];
        
        $this->assertEquals('/tech/news/new-gadget/', $generator->getUrl($template, $placeholders));
    }
    
    public function testGetUrlPermalink()
    {
        $generator = new UrlGenerator();
        $template = 'http://my-site.com/blog';
        
        $this->assertEquals('http://my-site.com/blog', $generator->getUrl($template));
    }
    
    /**
     * @expectedException \UnexpectedValueException
     */
    public function testGetUrlTemplateWhiteSpaces()
    {
        $generator = new UrlGenerator();
        $template = '/:categories/:title';
        $placeholders = [
            ':categories' => 'tech/news',
            ':title' => 'new gadget',
        ];
        
        $generator->getUrl($template, $placeholders);
    }
}