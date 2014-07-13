<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\Spress\Core\Tests\Plugin\API;

use Yosymfony\Spress\Core\Application;
use Yosymfony\Spress\Core\Plugin\API\TemplateManager;

class TemplateManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $app;
    protected $templateManager;
    
    public function setUp()
    {
        $this->app = new Application();
        $this->app['spress.config']->loadLocal(__DIR__ . '/../../fixtures/project');
        $this->templateManager = new TemplateManager($this->app['spress.cms.renderizer']);
    }
    
    public function testRender()
    {
        $this->assertEquals('Hi Spress', $this->templateManager->render('Hi {{ name }}', ['name' => 'Spress']));
    }
}