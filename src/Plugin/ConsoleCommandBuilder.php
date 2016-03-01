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
        $pluginsCollection = $this->pluginManager->getPluginCollection();

        foreach ($pluginsCollection as $plugin) {
            if ($this->isValidCommandPlugin($plugin) === true) {
                $result[] = $this->buildCommand($plugin);
            }
        }

        return $result;
    }

    /**
     * Build a Symfony Console commands.
     *
     * @param \Yosymfony\Spress\Plugin\CommandPluginInterface $commandPlugin
     *
     * @return \Symfony\Component\Console\Command\Command Symfony Console command.
     */
    protected function buildCommand(CommandPluginInterface $commandPlugin)
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
        $consoleComand->setCode(function (InputInterface $input, OutputInterface $output) use ($commandPlugin) {
            $io = new ConsoleIO($input, $output);
            $arguments = $input->getArguments();
            $options = $input->getOptions();

            $commandPlugin->executeCommand($io, $arguments, $options);
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
}
