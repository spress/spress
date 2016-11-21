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
use Yosymfony\Spress\Scaffolding\SiteGenerator;

/**
 * New site command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewSiteCommand extends BaseCommand
{
    /** @var string */
    const BLANK_THEME = 'blank';

    /** @var string */
    const SPRESSO_THEME = 'spresso';

    /** @var string */
    const SPRESS_INSTALLER_PACKAGE = 'spress/spress-installer >= 2.1';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition([
            new InputArgument('path', InputArgument::OPTIONAL, 'Path of the new site', './'),
            new InputArgument('theme', InputArgument::OPTIONAL, 'Theme name', self::BLANK_THEME),
            new InputOption('force', '', InputOption::VALUE_NONE, 'Force creation even if path already exists'),
            new InputOption('all', '', InputOption::VALUE_NONE, 'Complete scaffold of a blank site.'),
            new InputOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation from package sources when possible, including VCS information.'),
            new InputOption('prefer-lock', '', InputOption::VALUE_NONE, 'If there is a "composer.lock" file in the theme, Spress will use the exact version declared in that'),
            new InputOption('no-scripts', null, InputOption::VALUE_NONE, 'Skips the execution of all scripts defined in composer.json file.'),
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
        $theme = $input->getArgument('theme');
        $io = new ConsoleIO($input, $output);

        if ($theme === self::SPRESSO_THEME) {
            $theme = 'spress/spress-theme-spresso';
        }

        $this->startingMessage($io, $theme);

        if ($input->getOption('all') === true) {
            $io->warning('You are using a deprecated option "--all"');
        }

        $generator = new SiteGenerator(
            $this->getPackageManager($input->getArgument('path'), $io),
            self::SPRESS_INSTALLER_PACKAGE
        );
        $generator->setSkeletonDirs([$this->getSkeletonsDir()]);
        $generator->generate(
            $input->getArgument('path'),
            $theme,
            $input->getOption('force'),
            [
                'prefer-source' => $input->getOption('prefer-source'),
                'prefer-lock' => $input->getOption('prefer-lock'),
                'no-scripts' => $input->getOption('no-scripts'),
            ]
        );

        $this->successMessage($io, $theme, $input->getArgument('path'));
    }

    /**
     * Writes the staring messages.
     *
     * @param ConsoleIO $io    Spress IO
     * @param string    $theme The theme
     */
    protected function startingMessage(ConsoleIO $io, $theme)
    {
        $io->newLine();
        $io->write(sprintf('<comment>Generating a site using the theme: "%s"...</comment>', $theme));
    }

    /**
     * Writes the success messages.
     *
     * @param ConsoleIO $io    Spress IO
     * @param string    $theme The theme
     * @param string    $path  The path
     */
    protected function successMessage(ConsoleIO $io, $theme, $path)
    {
        $io->success(sprintf('New site with theme "%s" created at "%s" folder', $theme, $path));
    }
}
