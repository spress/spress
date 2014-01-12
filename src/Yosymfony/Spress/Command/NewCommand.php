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
use Yosymfony\Spress\Application;

class NewCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('site:new')
            ->setDescription('Create a new site scaffold')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Path of the new site',
                './'
            )
            ->addArgument(
                'template',
                InputArgument::OPTIONAL,
                'Template name',
                'blank'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force creation event if path already exists'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'Complete scaffold'
            );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $template = $input->getArgument('template');
        $force = $input->getOption('force');
        $completeScaffold = $input->getOption('all');
        
        $app = new Application();
        $app['spress.operation.new']->newSite($path, $template, $force, $completeScaffold);
        
        $output->writeln(sprintf('<comment>New site created at %s.</comment>', $path));
        
        if('./' == $path)
        {
            $output->writeln('<comment>Edit composer.json file to add your theme data and plugins required.</comment>');
        }
        else
        {
            $output->writeln(sprintf('<comment>Go to %s folder and edit composer.json file to add your theme data and plugins required.</comment>', $path));
        }    
    }
}