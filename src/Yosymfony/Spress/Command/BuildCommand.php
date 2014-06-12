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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yosymfony\Spress\Application;
use Yosymfony\Spress\IO\ConsoleIO;

class BuildCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('site:build')
            ->setDescription('Build your site')
            ->addOption(
                'source',
                's',
                InputOption::VALUE_REQUIRED,
                'Directory where Spress will read your files'
            )
            ->addOption(
                'timezone',
                null,
                InputOption::VALUE_REQUIRED,
                'Timezone for the site generator'
            )
            ->addOption(
                'env',
                null,
                InputOption::VALUE_REQUIRED,
                'Name of the environment configuration'
            )
            ->addOption(
                'drafts',
                null,
                InputOption::VALUE_NONE,
                'Parse your draft post'
            )
            ->addOption(
                'safe',
                null,
                InputOption::VALUE_NONE,
                'Disable your template plugins'
            );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $timezone = $input->getOption('timezone');
        $drafts = $input->getOption('drafts');
        $safe = $input->getOption('safe');
        $env = $input->getOption('env');
        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        
        $app = new Application([
            'spress.io' => $io,
        ]);
        
        $config = $app['spress.config'];
        $envDefault = $config->getEnvironmentName();
        
        $io->write('<comment>Starting...</comment>');
        $io->write(sprintf('<comment>Environment: %s.</comment>', $env ? $env : $envDefault));
        
        if($drafts)
        {
            $io->write('<comment>Posts drafts activated.</comment>');
        }
        
        if($safe)
        {
            $io->write('<comment>Plugins disabled.</comment>');
        }
        
        $resultData = $app->parse(
            $input->getOption('source'),
            $env,
            $timezone,
            $drafts,
            $safe
        );
        
        $io->write(sprintf('Total posts: %d', $resultData['total_post']));
        $io->write(sprintf('Processed posts: %d', $resultData['processed_post']));
        $io->write(sprintf('Drafts post: %d', $resultData['drafts_post']));
        $io->write(sprintf('Total pages: %d', $resultData['total_pages']));
        $io->write(sprintf('Processed pages: %d', $resultData['processed_pages']));
        $io->write(sprintf('Other resources: %d', $resultData['other_resources']));
    }
}