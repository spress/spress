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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yosymfony\Spress\Scaffolding\PostGenerator;
use Yosymfony\Spress\IO\ConsoleIO;

/**
 * New post command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewPostCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition([
            new InputOption('title', '', InputOption::VALUE_REQUIRED, 'The name of the post'),
            new InputOption('layout', '', InputOption::VALUE_REQUIRED, 'The layout of the post'),
            new InputOption('date', '', InputOption::VALUE_REQUIRED, 'The date assigned to the post'),
            new InputOption('tags', '', InputOption::VALUE_REQUIRED, 'Comma separated list of tags'),
            new InputOption('categories', '', InputOption::VALUE_REQUIRED, 'Comma separated list of categories'),
        ])
        ->setName('new:post')
        ->setDescription('Generate a post')
        ->setHelp(
            <<<'EOT'
The <info>new:post</info> command helps you generates new posts.
By default, the command interacts with the user to tweak the generation.
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

        $title = Validators::validatePostTitle($input->getOption('title'));
        $layout = $input->getOption('layout');
        $date = $input->getOption('date') ?: $this->getDateFormated();
        $tags = array_map('trim', explode(',', $input->getOption('tags') ?: ''));
        $categories = array_map('trim', explode(',', $input->getOption('categories') ?: ''));

        $postsDir = './src/content/posts';

        $generator = new PostGenerator();
        $generator->setSkeletonDirs([$this->getSkeletonsDir()]);

        $files = $generator->generate($postsDir, new \DateTime($date), $title, $layout, $tags, $categories);

        $this->resultMessage($io, $files);
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleIO($input, $output);

        $this->welcomeMessage($io);

        $title = $io->askAndValidate(
            'Post title',
            function ($answer) {
                return Validators::validatePostTitle($answer);
            },
            false,
            $input->getOption('title')
        );
        $input->setOption('title', $title);

        $layout = $io->ask('Post layout', $input->getOption('layout'));
        $input->setOption('layout', $layout);

        $defaultDate = $input->getOption('date');

        if (empty($defaultDate) === true) {
            $defaultDate = $this->getDateFormated();
        }

        $date = $io->ask('Post date', $defaultDate);
        $input->setOption('date', $date);

        if ($io->askConfirmation('Do you want to use tags?', empty($input->getOption('tags')) === false)) {
            $tags = $io->ask('Comma separated list of post tags', $input->getOption('tags'));
            $input->setOption('tags', $tags);
        }

        if ($io->askConfirmation('Do you want to use categories?', empty($input->getOption('categories')) === false)) {
            $categories = $io->ask('Comma separated list of post categories', $input->getOption('categories'));
            $input->setOption('categories', $categories);
        }
    }

    protected function getDateFormated()
    {
        $date = new \DateTime();

        return $date->format('Y-m-d');
    }

    protected function welcomeMessage($io)
    {
        $io->newLine();
        $io->write('Welcome to Spress <comment>post generator</comment>');
        $io->newLine();
    }

    protected function resultMessage($io, $files)
    {
        $io->success('The post was generated successfully!');
        $io->write('<comment>Files afected:</comment>');
        $io->listing($files);
        $io->newLine();
    }
}
