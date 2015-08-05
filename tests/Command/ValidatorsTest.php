<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Tests\Command;

use Yosymfony\Spress\Command\Validators;

class ValidatorsTest extends \PHPUnit_Framework_TestCase
{
    public function testValidatePluginName()
    {
        $this->assertEquals('yosymfony/testplugin', Validators::validatePluginName('yosymfony/testplugin'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateUpperPluginName()
    {
        Validators::validatePluginName('yosymfony/TestPlugin');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidatePluginNameWithoutVendor()
    {
        Validators::validatePluginName('TestPlugin');
    }

    public function testValidatePostTitle()
    {
        $this->assertEquals('The title', Validators::validatePostTitle('The title'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateEmptyPostTitle()
    {
        Validators::validatePluginName('');
    }

    public function testValidateEmail()
    {
        $this->assertEquals('example@example.com', Validators::validateEmail('example@example.com'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateInvalidEmail()
    {
        Validators::validateEmail('@example.com');
    }
}
