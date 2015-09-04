<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Plugin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yosymfony\Spress\Core\Plugin\PluginInterface;
use Yosymfony\Spress\Core\Plugin\PluginManager;
use Yosymfony\Spress\IO\ConsoleIO;

/**
 * Symfony Console Command builder for command plugins.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConsoleCommandBuilder
{
    protected $pluginManager;

    /**
     * Constructor.
     *
     * @param PluginManager $pluginManager
     */
    public function __construct(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * Gets a list of Symfony Console command.
     *
     * @return Symfony\Component\Console\Command\Command[] Symfony Console commands
     */
    public function buildCommands()
    {
        $result = [];
        $plugins = $this->pluginManager->getPlugins();
        $helperSet = $this->getDefaultHelperSet();

        foreach ($plugins as $plugin) {
            if ($this->isValidCommandPlugin($plugin) === true) {
                $result[] = $this->buildCommand($plugin, $helperSet);
            }
        }

        return $result;
    }

    /**
     * Build a Symfony Console commands.
     *
     * @param \Yosymfony\Spress\Plugin\CommandPluginInterface $commandPlugin
     * @param \Symfony\Component\Console\Helper\HelperSet     $helperSet     Helper set.
     *
     * @return \Symfony\Component\Console\Command\Command Symfony Console command.
     */
    protected function buildCommand(CommandPluginInterface $commandPlugin, HelperSet $helperSet)
    {
        $definition = $commandPlugin->getCommandDefinition();
        $argumentsAndOptions = [];

        $consoleComand = new Command($definition->getName());
        $consoleComand->setDescription($definition->getDescription());
        $consoleComand->setHelp($definition->getHelp());

        foreach ($definition->getArguments() as list($name, $mode, $description, $defaultValue)) {
            $argumentsAndOptions[] = new InputArgument($name, $mode, $description, $defaultValue);
        }

        foreach ($definition->getOptions() as list($name, $shortcut, $mode, $description, $defaultValue)) {
            $argumentsAndOptions[] = new InputOption($name, $shortcut, $mode, $description, $defaultValue);
        }

        $consoleComand->setDefinition($argumentsAndOptions);
        $consoleComand->setCode(function (InputInterface $input, OutputInterface $output) use ($commandPlugin, $helperSet) {
            $io = new ConsoleIO($input, $output, $helperSet);
            $commandPlugin->executeCommand($io);
        });

        return $consoleComand;
    }

    /**
     * Is a valid command plugin?
     *
     * @param \Yosymfony\Spress\Core\Plugin\PluginInterface
     *
     * @return bool
     */
    protected function isValidCommandPlugin(PluginInterface $plugin)
    {
        $implements = class_implements($plugin);

        return isset($implements['Yosymfony\Spress\Plugin\CommandPluginInterface']);
    }

    /**
     * Gets the default helper set with the helpers that should always be available.
     *
     * @return HelperSet A HelperSet instance.
     */
    protected function getDefaultHelperSet()
    {
        return new HelperSet(array(
            new FormatterHelper(),
            new DebugFormatterHelper(),
            new ProcessHelper(),
            new QuestionHelper(),
        ));
    }
}
