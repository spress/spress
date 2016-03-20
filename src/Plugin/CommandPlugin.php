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

use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\Core\Plugin\EventSubscriber;
use Yosymfony\Spress\Plugin\Environment\CommandEnvironmentInterface;
use Yosymfony\Spress\Plugin\Environment\DefaultCommandEnvironment;

/**
 * Base class for a command plugin.
 *
 * @api
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class CommandPlugin implements CommandPluginInterface
{
    protected $symfonyCommand;
    protected $output;
    protected $environment;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->environment = new DefaultCommandEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandDefinition()
    {
        throw new \RuntimeException('You must override the "getCommandDefinition" method in the concrete command plugin class.');
    }

    /**
     * {@inheritdoc}
     */
    public function executeCommand(IOInterface $io, array $arguments, array $options)
    {
        throw new \RuntimeException('You must override the "executeCommand" method in the concrete command plugin class.');
    }

    /**
     * {@inheritdoc}
     */
    public function getMetas()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * Override this method in case you need a regular plugin behavior.
     */
    public function initialize(EventSubscriber $subscriber)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setCommandEnvironment(CommandEnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandEnvironment()
    {
        return $this->environment;
    }
}
