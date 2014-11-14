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
 * New plugin command
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
            new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace of the plugin', ''),
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

        $name = $input->getOption('name');
        $namespace = $input->getOption('namespace') ?: '';
        $author = $input->getOption('author');
        $email = $input->getOption('email');
        $description = $input->getOption('description');
        $license = $input->getOption('license') ?: 'MIT';

        $app = new SpressCLI($io);

        $config = $app['spress.config'];
        $config->loadLocal('./', 'dev');

        $generator = new PluginGenerator();
        $generator->setSkeletonDirs($app['spress.paths']['skeletons']);

        $pluginDir = $config->getRepository()->get('plugins');

        $files = $generator->generate($pluginDir, $name, $namespace, $author, $email, $description, $license);

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

        // Name:
        $name = $input->getOption('name');
        $question = new Question('Plugin name <info>(follow the pattern</info> <comment>"vendor-name/plugin-name"</comment><info>)</info>: ', $name);
        $question->setMaxAttempts(null);
        $question->setValidator(function ($answer) {
            return Validators::validatePluginName($answer);
        });
        $name = $helper->ask($input, $output, $question);
        $input->setOption('name', $name);

        // Namespace:
        $this->namespaceMessage($io);

        $namespace = $input->getOption('namespace');
        $question = new Question('Plugin namespace (global): ', $namespace);
        $question->setValidator(function ($answer) {
            return Validators::validateNamespace($answer);
        });
        $namespace = $helper->ask($input, $output, $question);
        $input->setOption('namespace', $namespace);

        // Author:
        $author = $input->getOption('author');
        $question = new Question('Plugin author: ', $author);
        $author = $helper->ask($input, $output, $question);
        $input->setOption('author', $author);

        // Email:
        $email = $input->getOption('email');
        $question = new Question('Email author: ', $email);
        $question->setValidator(function ($answer) {
            if(0 === strlen($answer)) {
                return $answer;
            }

            return Validators::validateEmail($answer);
        });
        $email = $helper->ask($input, $output, $question);
        $input->setOption('email', $email);

        // Description:
        $description = $input->getOption('description');
        $question = new Question('Plugin description: ', $description);
        $description = $helper->ask($input, $output, $question);
        $input->setOption('description', $description);

        // License:
        $this->licenseMessage($io);

        $license = $input->getOption('license');
        $question = new Question('Plugin license (MIT): ', $license);
        $license = $helper->ask($input, $output, $question);
        $input->setOption('license', $license);
    }

    protected function getPluginDir($app)
    {
        return $dir ?: $app['spress.config']->getRepository()->get('plugins');
    }

    protected function welcomeMessage($io)
    {
        $io->write([
            '',
            'Welcome to <comment>Spress plugin generator</comment>',
            '',
        ]);
    }

    protected function namespaceMessage($io)
    {
        $io->write([
            '',
            '<comment>The global namespace is the option recommended.</comment>',
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
