<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Tests\Console;

use Yosymfony\Spress\Console\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $autoloaders = spl_autoload_functions();

        $this->app = new Application($autoloaders[0][0]);
    }

    public function testGetClassloader()
    {
        $this->assertInstanceOf('Composer\Autoload\ClassLoader', $this->app->getClassloader());
    }

    public function testGetSpress()
    {
        $this->assertInstanceOf('Yosymfony\Spress\Core\Spress', $this->app->getSpress());
    }

    public function testRegisterStandardCommands()
    {
        $this->app->registerStandardCommands();

        $this->assertTrue(count($this->app->all()) > 0);
    }
}
