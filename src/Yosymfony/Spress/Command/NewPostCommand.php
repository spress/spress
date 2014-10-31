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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * New Post command
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class NewPostCommand extends Command
{
	protected function configure()
	{
		$this
            ->setName('new:post')
            ->setDescription('Generate a post')
            ->addOption(
                'title',
                null,
                InputOption::VALUE_REQUIRED,
                'The name of the post'
            )
            ->addOption(
            	'date',
            	null,
            	InputOption::VALUE_REQUIRED,
            	'The date assigned to the post with ISO 8601 format.'
            )
            ->addOption(
            	'tags',
            	null,
            	InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            	'Tags list separed by white spaces'
            )
            ->addOption(
            	'categories',
            	null,
            	InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            	'Tags list separed by white spaces'
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$helper = $this->getHelper('question');

        $output->writeln([
            '',
            'Welcome to post generator',
            ''
        ]);

		$question = new Question('Post title: ');
        $title = $helper->ask($input, $output, $question);

        $dateFormated = $this->getDateFormated();
        $question = new Question("Post date ($dateFormated): ", $dateFormated);
        $dateName = $helper->ask($input, $output, $question);

        $question = new Question('List of post tags separed by white space: ', '');
        $tags = $helper->ask($input, $output, $question);

        $question = new Question('List of post categories separed by white space: ', '');
        $categories = $helper->ask($input, $output, $question);
	}

    protected function getDateFormated()
    {
        $date = new \DateTime();

        return $date->format('Y-m-d');
    }
}
