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
use Symfony\Component\Console\Question\Question;
use Yosymfony\Spress\Scaffolding\PluginGenerator;
use Yosymfony\Spress\IO\ConsoleIO;

/**
 * New plugin command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewPluginCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition([
            new InputOption('name', '', InputOption::VALUE_REQUIRED, 'The name of the plugins should follow the pattern "vendor-name/plugin-name"'),
            new InputOption('command-name', '', InputOption::VALUE_REQUIRED, 'The name of the command. e.g: "foo" or "foo:bar" namespace separated by colon (:)'),
            new InputOption('command-description', '', InputOption::VALUE_REQUIRED, 'The description of the command'),
            new InputOption('command-help', '', InputOption::VALUE_REQUIRED, 'The help of the command'),
            new InputOption('author', '', InputOption::VALUE_REQUIRED, 'The author of the plugin'),
            new InputOption('email', '', InputOption::VALUE_REQUIRED, 'The Email of the author'),
            new InputOption('description', '', InputOption::VALUE_REQUIRED, 'The description of your plugin'),
            new InputOption('license', '', InputOption::VALUE_REQUIRED, 'The license under you publish your plugin'),
        ])
        ->setName('new:plugin')
        ->setDescription('Generate a plugin')
        ->setHelp(<<<EOT
The <info>new:plugin</info> command helps you generates new plugins.
By default, the command interacts with the developer to tweak the generation.
Any passed option will be used as a default value for the interaction.
If you want to disable any user interaction, use <comment>--no-interaction</comment>.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleIO($input, $output);

        $name = Validators::validatePluginName($input->getOption('name'));

        $commandName = $input->getOption('command-name');

        if (empty($commandName) === false) {
            $commandName = Validators::validateCommandName($commandName);
        }

        $commandDescription = $input->getOption('command-description');
        $commandHelp = $input->getOption('command-help');
        $author = $input->getOption('author');

        $email = $input->getOption('email');

        if (empty($email) === false) {
            $email = Validators::validateEmail($email);
        }

        $description = $input->getOption('description');
        $license = $input->getOption('license') ?: 'MIT';

        $generator = new PluginGenerator('./src/plugins', $name);
        $generator->setSkeletonDirs(__DIR__.'/../../app/skeletons');
        $generator->setCommandData($commandName, $commandDescription, $commandHelp);
        $generator->setAuthor($author, $email);
        $generator->setDescription($description);
        $generator->setLicense($license);

        $files = $generator->generate();

        $this->resultMessage($io, $files);
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $io = new ConsoleIO($input, $output);

        $this->welcomeMessage($io);

        $name = $io->askAndValidate(
            'Plugin name (follow the pattern <comment>"vendor-name/plugin-name"</comment>)',
            function ($answer) {
                return Validators::validatePluginName($answer);
            },
            false,
            $input->getOption('name')
        );

        $input->setOption('name', $name);

        if ($io->askConfirmation('Is it a command plugin?', empty($input->getOption('name')) === false)) {
            $commandName = $io->askAndValidate(
                'Name of the command',
                function ($answer) {
                    return Validators::validateCommandName($answer);
                },
                false,
                $input->getOption('command-name')
            );

            $input->setOption('command-name', $commandName);

            $commandDescription = $io->ask('Description for the command', $input->getOption('command-description'));
            $input->setOption('command-description', $commandDescription);

            $commandHelp = $io->ask('Help for the command', $input->getOption('command-help'));
            $input->setOption('command-help', $commandHelp);
        }

        $author = $io->ask('Plugin author', $input->getOption('author'));
        $input->setOption('author', $author);

        $email = $io->askAndValidate(
            'Email author',
            function ($answer) {
                return Validators::validateEmail($answer);
            },
            false,
            $input->getOption('email')
        );
        $input->setOption('email', $email);

        $description = $io->ask('Plugin description', $input->getOption('description'));
        $input->setOption('description', $description);

        $this->licenseMessage($io);

        $defaultLicense = $input->getOption('license');

        if (is_null($defaultLicense) === true) {
            $defaultLicense = 'MIT';
        }

        $license = $io->ask('Plugin license', $defaultLicense);
        $input->setOption('license', $license);
    }

    protected function welcomeMessage($io)
    {
        $io->newLine();
        $io->write('Welcome to Spress <comment>plugin generator</comment>');
        $io->newLine();
    }

    protected function licenseMessage($io)
    {
        $io->write('<comment>The most common licenses:</comment>');
        $io->listing(['Apache-2.0', 'BSD-2-Clause', 'GPL-3.0', 'LGPL-3.0', 'MIT']);
        $io->write('<comment>You can find more information at http://spdx.org/licenses/</comment>');
    }

    protected function resultMessage($io, $files)
    {
        $io->success('The plugin was generated successfully!');
        $io->write('<comment>Files afected:</comment>');
        $io->listing($files);
        $io->write('<comment>Finally you can add your plugin to the add-on list at <info>http://spress.yosymfony.com/add-ons/</info></comment>');
        $io->newLine();
    }
}
