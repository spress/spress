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
use Yosymfony\Spress\Scaffolding\PostGenerator;
use Yosymfony\Spress\IO\ConsoleIO;

/**
 * New post command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewPostCommand extends Command
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
        ->setHelp(<<<EOT
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
        $io = new ConsoleIO($input, $output, $this->getHelperSet());

        $title = Validators::validatePostTitle($input->getOption('title'));
        $layout = $input->getOption('layout');
        $date = $input->getOption('date') ?: $this->getDateFormated();
        $tags = array_map('trim', explode(',', $input->getOption('tags') ?: ''));
        $categories = array_map('trim', explode(',', $input->getOption('categories') ?: ''));

        $postsDir = './src/content/posts';

        $generator = new PostGenerator();
        $generator->setSkeletonDirs(__DIR__.'/../../app/skeletons');

        $files = $generator->generate($postsDir, new \DateTime($date), $title, $layout, $tags, $categories);

        $this->resultMessage($io, $files);
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $io = new ConsoleIO($input, $output, $this->getHelperSet());

        $this->welcomeMessage($io);

        $title = $input->getOption('title');
        $question = new Question('Post title: ', $title);
        $question->setMaxAttempts(null);
        $question->setValidator(function ($answer) {
            return Validators::validatePostTitle($answer);
        });
        $title = $helper->ask($input, $output, $question);
        $input->setOption('title', $title);

        $layout = $input->getOption('layout');
        $question = new Question('Post layout: ', $layout);
        $layout = $helper->ask($input, $output, $question);
        $input->setOption('layout', $layout);

        $date = $input->getOption('date') ?: $this->getDateFormated();
        $question = new Question("Post date [<comment>$date</comment>]: ", $date);
        $date = $helper->ask($input, $output, $question);
        $input->setOption('date', $date);

        $tags = $input->getOption('tags') ?: '';
        $question = new Question('Comma separated list of post tags: ', $tags);
        $tags = $helper->ask($input, $output, $question);
        $input->setOption('tags', $tags);

        $categories = $input->getOption('categories') ?: '';
        $question = new Question('Comma separated list of post categories: ', $categories);
        $categories = $helper->ask($input, $output, $question);
        $input->setOption('categories', $categories);
    }

    protected function getDateFormated()
    {
        $date = new \DateTime();

        return $date->format('Y-m-d');
    }

    protected function welcomeMessage($io)
    {
        $io->write([
            '',
            '<comment>Welcome to Spress <info>post generator</info></comment>',
            '',
        ]);
    }

    protected function resultMessage($io, $files)
    {
        $io->write([
            '',
            '<success>The post was generated successfully!</success>',
            '',
            '<comment>File afected:</comment>',
            '',
        ]);
        $io->write($files);
        $io->write('');
    }
}
