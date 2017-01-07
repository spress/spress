<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Plugin\Environment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Yosymfony\Spress\Console\Application;

/**
 * Command environment implementation based on Symfony Console.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SymfonyCommandEnvironment implements CommandEnvironmentInterface
{
    protected $symfonyCommand;
    protected $output;

    /**
     * Constructor.
     *
     * @param Symfony\Component\Console\Command\Command        $symfonyCommand The Symfony Console command
     * @param Symfony\Component\Console\Output\OutputInterface $output         The output
     */
    public function __construct(Command $symfonyCommand, OutputInterface $output)
    {
        $this->symfonyCommand = $symfonyCommand;
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCommand($name)
    {
        $symfonyConsoleApp = $this->getSymfonyConsoleApplication();

        return $symfonyConsoleApp->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function runCommand($commandName, array $arguments)
    {
        if ($this->hasCommand($commandName) === false) {
            throw new CommandNotFoundException(sprintf('Command "%s" not found.', $commandName));
        }

        $symfonyConsoleApp = $this->getSymfonyConsoleApplication();
        $commandToRun = $symfonyConsoleApp->find($commandName);

        $arguments['command'] = $commandName;

        $arrayInput = new ArrayInput($arguments);

        return $commandToRun->run($arrayInput, $this->output);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpress($siteDir = null)
    {
        $symfonyConsoleApp = $this->getSymfonyConsoleApplication();

        if ($symfonyConsoleApp instanceof Application) {
            return $symfonyConsoleApp->getSpress($siteDir);
        }

        throw new \RuntimeException('This implementation of the Symfony Console application does not support Spress.');
    }

    /**
     * Gets the Symfony Console application.
     *
     * @return Yosymfony\Spress\Console\Application The Symfony Console application
     *
     * @throws LogicException If Symfony Console application is not set up
     */
    protected function getSymfonyConsoleApplication()
    {
        if (is_null($application = $this->symfonyCommand->getApplication()) === true) {
            throw new \LogicException('Symfony Console commands need a console application set up.');
        }

        return $application;
    }
}
