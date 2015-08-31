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

/**
 * Definition of a command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class CommandDefinition
{
    const REQUIRED = 1;
    const OPTIONAL = 2;
    const IS_ARRAY = 4;

    const VALUE_NONE = 1;
    const VALUE_REQUIRED = 2;
    const VALUE_OPTIONAL = 4;
    const VALUE_IS_ARRAY = 8;

    private $name;
    private $help;
    private $description;
    private $arguments;
    private $options;

    /**
     * Constructor.
     *
     * @param string $name The name of the command. e.g: "update" or with namespace "theme:update".
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->arguments = [];
        $this->options = [];
    }

    /**
     * Gets the name of the command.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the description for the command.
     *
     * @param string $description The description for the command
     *
     * @return Command The current instance
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns the description for the command.
     *
     * @return string The description for the command.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the help for the command.
     *
     * @param string $help The help for the command.
     *
     * @return Command The current instance.
     */
    public function setHelp($help)
    {
        $this->help = $help;

        return $this;
    }
    /**
     * Returns the help for the command.
     *
     * @return string The help for the command.
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Adds a new command argument.
     *
     * @param string $name        The argument name.
     * @param int    $mode        The argument mode: self::REQUIRED or self::OPTIONAL.
     * @param string $description A description text.
     * @param mixed  $default     The default value (for self::OPTIONAL mode only).
     *
     * @throws \InvalidArgumentException When argument mode is not valid.
     */
    public function addArgument($name, $mode = null, $description = '', $default = null)
    {
        if (null === $mode) {
            $mode = self::OPTIONAL;
        } elseif (!is_int($mode) || $mode > 7 || $mode < 1) {
            throw new \InvalidArgumentException(sprintf('Argument mode "%s" is not valid.', $mode));
        }

        $this->arguments[] = [$name, $mode, $description, $default];
    }

    /**
     * Adds a new command option.
     *
     * @param string       $name        The option name.
     * @param string|array $shortcut    The shortcuts, can be null, a string of shortcuts delimited
     *                                  by | or an array of shortcuts.
     * @param int          $mode        The option mode: One of the VALUE_* constants.
     * @param string       $description A description text.
     * @param mixed        $default     The default value (must be null for self::REQUIRED or self::VALUE_NONE).
     *
     * @throws \InvalidArgumentException If name is empty or option mode is invalid or incompatible.
     */
    public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('An option name cannot be empty.');
        }

        if (null === $mode) {
            $mode = self::VALUE_NONE;
        } elseif (!is_int($mode) || $mode > 15 || $mode < 1) {
            throw new \InvalidArgumentException(sprintf('Option mode "%s" is not valid.', $mode));
        }

        $this->options[] = [$name, $shortcut, $mode, $description, $default];
    }

    /**
     * Gets the arguments registered.
     *
     * @return array Each element is an array with the following signature: name, mode, description, default.
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Gets the options registered.
     *
     * @return array Each element is an array with the following signature: name, shortcut, mode, description, default.
     */
    public function getOptions()
    {
        return $this->options;
    }
}
