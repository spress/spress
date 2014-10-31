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

class NewPluginCommand extends Command
{
	protected function configure()
	{
		$this
            ->setName('new:plugin')
            ->setDescription('Create a plugin')
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'The name of the plugins should follow the pattern "vendor-name/plugin-name".'
            )
            ->addOption(
            	'namespace',
            	null,
            	InputOption::VALUE_REQUIRED,
            	'The namespace of the plugin',
                ''
            )
            ->addOption(
            	'author',
            	null,
            	InputOption::VALUE_REQUIRED,
            	'The author of the plugin'
            );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$question = new Question('Plugin name: ');
        $name = $helper->ask($input, $output, $question);

        $question = new Question('Plugin namespace (global): ');
        $namespace = $helper->ask($input, $output, $question);

        $question = new Question('Plugin author: ');
        $author = $helper->ask($input, $output, $question);
	}
}
