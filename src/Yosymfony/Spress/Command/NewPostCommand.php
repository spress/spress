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
use Yosymfony\Spress\Scaffolding\PostGenerator;
use Yosymfony\Spress\IO\ConsoleIO;

/**
 * New post command
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewPostCommand extends Command
{
    /**
     * @see Symfony\Component\Console\Command\Command
     */
	protected function configure()
	{
        $this->setDefinition([
            new InputOption('title', '', InputOption::VALUE_REQUIRED, 'The name of the post'),
            new InputOption('layout', '', InputOption::VALUE_REQUIRED, 'The layout of the post'),
            new InputOption('date', '', InputOption::VALUE_REQUIRED, 'The date assigned to the post'),
            new InputOption('tags', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Tags list separed by white spaces'),
            new InputOption('categories', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Categories list separed by white spaces'),
        ])
        ->setName('new:post')
        ->setDescription('Generate a post')
        ->setHelp(<<<EOT
The <info>new:post</info> command helps you generates new posts.
By default, the command interacts with the developer to tweak the generation.
Any passed option will be used as a default value for the interaction.
If you want to disable any user interaction, use <comment>--no-interaction</comment>.
EOT
            );
	}

    /**
     * @see Symfony\Component\Console\Command\Command
     */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
        $io = new ConsoleIO($input, $output, $this->getHelperSet());

        $title = $input->getOption('title');
        $layout = $input->getOption('layout');
        $date = $input->getOption('date') ?: $this->getDateFormated();
        $tags = explode(' ', $input->getOption('tags') ?: '');
        $categories = explode(' ', $input->getOption('categories') ?: '');

        $app = new SpressCLI($io);

        $config = $app['spress.config'];
        $config->loadLocal('./', 'dev');

        $postsDir = $config->getRepository()->get('posts');

        $generator = new PostGenerator();
        $generator->setSkeletonDirs($app['spress.paths']['skeletons']);
        
        $generator->generate($postsDir, new \DateTime($date), $title, $layout, $tags, $categories);

        $this->resultMessage($io, $this->files);
	}

    /**
     * @see Symfony\Component\Console\Command\Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        
        $this->welcomeMessage($io);

        // Title:
        $title = $input->getOption('title');
        $question = new Question('Post title: ', $title);
        $question->setMaxAttempts(null);
        $question->setValidator(function($answer)
        {
            if(0 == strlen($answer))
            {
                throw new \RuntimeException('The post title should not be empty.');
            }

            return $answer;
        });
        $title = $helper->ask($input, $output, $question);
        $input->setOption('title', $title);

        // Layout:
        $layout = $input->getOption('layout') ?: 'default';
        $question = new Question('Post layout (default): ', $layout);
        $layout = $helper->ask($input, $output, $question);
        $input->setOption('layout', $layout);

        // Date:
        $date = $input->getOption('date') ?: $this->getDateFormated();
        $question = new Question("Post date ($date): ", $date);
        $date = $helper->ask($input, $output, $question);
        $input->setOption('date', $date);

        // Tags:
        $tags = $input->getOption('tags') ?: '';
        $question = new Question('List of post tags separed by white space: ', $tags);
        $input->setOption('tags', $tags);

        // Categories:
        $categories = $input->getOption('categories') ?: '';
        $question = new Question('List of post categories separed by white space: ', $categories);
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
            'Welcome to <comment>Spress post generator</comment>',
            ''
        ]);
    }

    protected function resultMessage($io, $files)
    {
        $io->write([
            '',
            '<comment>File afected:</comment>',
            '',
        ]);
        $io->write($files);
    }
}
