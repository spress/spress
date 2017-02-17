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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yosymfony\Spress\IO\ConsoleIO;
use Yosymfony\Spress\Scaffolding\ThemeGenerator;

/**
 * New theme command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewThemeCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition(array(
            new InputArgument('path', InputArgument::OPTIONAL, 'Path of the new theme', getcwd()),
            new InputArgument('package', InputArgument::OPTIONAL, 'The name of the theme package.', ThemeGenerator::BLANK_THEME),
            new InputOption('repository', null, InputOption::VALUE_REQUIRED, 'Pick a different repository (as url or json config) to look for the theme package.'),
            new InputOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation of the theme from package sources when possible, including VCS information.'),
            new InputOption('dev', null, InputOption::VALUE_NONE, 'Enables installation of dev-require packages of the theme.'),
            new InputOption('no-scripts', null, InputOption::VALUE_NONE, 'Skips the execution of all scripts defined in composer.json file.'),
            new InputOption('prefer-lock', '', InputOption::VALUE_NONE, 'If there is a "composer.lock" file in the theme, Spress will use the exact version declared in that'),
            new InputOption('force', '', InputOption::VALUE_NONE, 'Force creation even if path already exists'),
        ))
            ->setName('new:theme')
            ->setDescription('Create a new theme')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command create a new blank theme or a new
theme based on another.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleIO($input, $output);
        $packageManager = $this->getPackageManager($input->getArgument('path'), $io);
        $generator = new ThemeGenerator($packageManager);
        $generator->setSkeletonDirs([$this->getSkeletonsDir()]);

        $this->startingMessage(
            $io,
            $input->getArgument('package'),
            $packageManager->getRootDirectory()
        );

        $options = [
            'prefer-source' => $input->getOption('prefer-source'),
            'repository' => $input->getOption('repository'),
        ];

        $generator->generate(
            $packageManager->getRootDirectory(),
            $input->getArgument('package'),
            $input->getOption('force'),
            $options
        );

        $this->updateRequirementsMessage($io);

        $options = array_merge($options, [
           'no-dev' => !$input->getOption('dev'),
           'no-scripts' => $input->getOption('no-scripts'),
        ]);

        if ($input->getOption('prefer-lock') === true) {
            $packageManager->install($options);
        } else {
            $packageManager->update($options);
        }

        $packageManager->rewritingSelfVersionDependencies();

        $this->okMessage($io);
    }

    /**
     * Writes the staring messages.
     *
     * @param ConsoleIO $io       Spress IO
     * @param string    $template The template
     */
    protected function startingMessage(ConsoleIO $io, $packageName, $siteDir)
    {
        $io->newLine();
        $io->write(sprintf(
            '<comment>Installing theme: "%s" in "%s" folder</comment>',
            $packageName,
            $siteDir
        ));
        $io->newLine();
    }

    protected function updateRequirementsMessage(ConsoleIO $io)
    {
        $io->newLine(2);
        $io->write('<comment>Updating requirements...</comment>');
        $io->newLine();
    }

    protected function okMessage(ConsoleIO $io)
    {
        $io->newLine();
        $io->success('Theme installed correctly.');
    }
}
