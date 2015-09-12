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
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
     * @see Symfony\Component\Console\Command\Command
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleIO($input, $output, $this->getHelperSet());

        $name = Validators::validatePluginName($input->getOption('name'));
        $commandName = Validators::validateCommandName($input->getOption('command-name'), true);
        $commandDescription = $input->getOption('command-description');
        $commandHelp = $input->getOption('command-help');
        $author = $input->getOption('author');
        $email = Validators::validateEmail($input->getOption('email'), true);
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
     * @see Symfony\Component\Console\Command\Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $io = new ConsoleIO($input, $output, $this->getHelperSet());

        $this->welcomeMessage($io);

        $name = $input->getOption('name');
        $question = new Question('Plugin name <info>(follow the pattern</info> <comment>"vendor-name/plugin-name"</comment><info>)</info>: ', $name);
        $question->setMaxAttempts(null);
        $question->setValidator(function ($answer) {
            return Validators::validatePluginName($answer);
        });
        $name = $helper->ask($input, $output, $question);
        $input->setOption('name', $name);

        $question = new ConfirmationQuestion('Is it a command plugin?[<comment>n</comment>]: ', false);
        $isCommandPlugin = $helper->ask($input, $output, $question);

        if ($isCommandPlugin === true) {
            $commandName = $input->getOption('command-name');
            $question = new Question('Name of the command: ', $commandName);
            $question->setMaxAttempts(null);
            $question->setValidator(function ($answer) {
                return Validators::validateCommandName($answer);
            });
            $commandName = $helper->ask($input, $output, $question);
            $input->setOption('command-name', $commandName);

            $commandDescription = $input->getOption('command-description');
            $question = new Question('Description for the command: ', $commandDescription);
            $commandDescription = $helper->ask($input, $output, $question);
            $input->setOption('command-description', $commandDescription);

            $commandHelp = $input->getOption('command-help');
            $question = new Question('Help for the command: ', $commandHelp);
            $commandHelp = $helper->ask($input, $output, $question);
            $input->setOption('command-help', $commandHelp);
        }

        $author = $input->getOption('author');
        $question = new Question('Plugin author: ', $author);
        $author = $helper->ask($input, $output, $question);
        $input->setOption('author', $author);

        $email = $input->getOption('email');
        $question = new Question('Email author: ', $email);
        $question->setValidator(function ($answer) {
            return Validators::validateEmail($answer, true);
        });
        $email = $helper->ask($input, $output, $question);
        $input->setOption('email', $email);

        $description = $input->getOption('description');
        $question = new Question('Plugin description: ', $description);
        $description = $helper->ask($input, $output, $question);
        $input->setOption('description', $description);

        $this->licenseMessage($io);

        $license = $input->getOption('license');
        $question = new Question('Plugin license [<comment>MIT</comment>]: ', $license);
        $license = $helper->ask($input, $output, $question);
        $input->setOption('license', $license);
    }

    protected function welcomeMessage($io)
    {
        $io->write([
            '',
            'Welcome to <comment>Spress plugin generator</comment>',
            '',
        ]);
    }

    protected function licenseMessage($io)
    {
        $io->write([
            '',
            '<comment>The most common licenses:</comment>',
            ' * Apache-2.0',
            ' * BSD-2-Clause',
            ' * GPL-3.0',
            ' * LGPL-3.0',
            ' * MIT',
            '',
            '<comment>You can find more information at http://spdx.org/licenses/</comment>',
            '',
        ]);
    }

    protected function resultMessage($io, $files)
    {
        $io->write([
            '',
            '<success>The plugin was generated successfully!</success>',
            '',
            '<comment>Files afected:</comment>',
            '',
        ]);

        $io->write($files);

        $io->write([
            '',
            '<comment>Finally you can add your plugin to the add-on list at http://spress.yosymfony.com/add-ons/</comment>',
            '',
        ]);
    }
}
