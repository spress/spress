<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yosymfony\Spress\IO\ConsoleIO;
use Yosymfony\Spress\Scaffolding\NewSite;

/**
 * New site command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewSiteCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition([
            new InputArgument('path', InputArgument::OPTIONAL, 'Path of the new site', './'),
            new InputArgument('template', InputArgument::OPTIONAL, 'Template name', 'blank'),
            new InputOption('force', '', InputOption::VALUE_NONE, 'Force creation event if path already exists'),
            new InputOption('all', '', InputOption::VALUE_NONE, 'Complete scaffold'),
        ])
        ->setName('new:site')
        ->setDescription('Create a new site')
        ->setHelp('The <info>new:site</info> command helps you generates new sites.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $template = $input->getArgument('template');
        $force = $input->getOption('force');
        $completeScaffold = $input->getOption('all');
        $io = new ConsoleIO($input, $output);

        $operation = new NewSite($this->getTemplatesPath());
        $operation->newSite($path, $template, $force, $completeScaffold);

        $this->successMessage($io, $path);
    }

    protected function getTemplatesPath()
    {
        $spressPath = __DIR__.'/../../';

        if (file_exists($spressPath.'app/templates/')) {
            return $spressPath.'app/templates';
        }

        return $spressPath.'../spress-templates';
    }

    protected function successMessage($io, $sitePath)
    {
        $io->success(sprintf('New site created at "%s" folder', $sitePath));
    }
}
