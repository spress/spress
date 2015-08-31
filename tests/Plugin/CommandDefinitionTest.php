<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\tests\Plugin;

use Yosymfony\Spress\Plugin\CommandDefinition;

class CommandDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandDefinitionSimple()
    {
        $definition = new CommandDefinition('self-update');

        $this->assertTrue(is_array($definition->getArguments()));
        $this->assertCount(0, $definition->getArguments());

        $this->assertTrue(is_array($definition->getOptions()));
        $this->assertCount(0, $definition->getOptions());
    }

    public function testCommandDefinitionWithArgumentsAndOptions()
    {
        $definition = new CommandDefinition('i18:extracts');
        $definition->setDescription('List of texts extracted from your site');
        $definition->setHelp('The <info>i18n:extracts</info> command help you to extract texts.');
        $definition->addArgument('file', null, 'File or directory', './');
        $definition->addOption('filter', 'f', null, 'Filter expression');

        $this->assertEquals('i18:extracts', $definition->getName());
        $this->assertEquals('List of texts extracted from your site', $definition->getDescription());
        $this->assertEquals('The <info>i18n:extracts</info> command help you to extract texts.', $definition->getHelp());

        $this->assertTrue(is_array($definition->getArguments()));
        $this->assertCount(1, $definition->getArguments());

        $argument = $definition->getArguments()[0];

        $this->assertCount(4, $argument);

        $this->assertEquals('file', $argument[0]);
        $this->assertEquals(CommandDefinition::OPTIONAL, $argument[1]);
        $this->assertEquals('File or directory', $argument[2]);
        $this->assertEquals('./', $argument[3]);

        $this->assertTrue(is_array($definition->getOptions()));
        $this->assertCount(1, $definition->getOptions());

        $option = $definition->getOptions()[0];

        $this->assertCount(5, $option);

        $this->assertEquals('filter', $option[0]);
        $this->assertEquals('f', $option[1]);
        $this->assertEquals(CommandDefinition::VALUE_NONE, $option[2]);
        $this->assertEquals('Filter expression', $option[3]);
        $this->assertNull($option[4]);
    }
}
