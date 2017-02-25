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

/**
 * Add plugin command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class RemovePluginCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition(array(
               new InputArgument('packages', InputArgument::IS_ARRAY, 'List of packages that should be removed.'),
               new InputOption('dev', null, InputOption::VALUE_NONE, 'Remove packages from require-dev.'),
               new InputOption('no-scripts', null, InputOption::VALUE_NONE, 'Skips the execution of all scripts defined in composer.json file.'),
           ))
            ->setName('remove:plugin')
            ->setDescription('Removes plugins and themes from the composer.json file of the current directory')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command removes a plugin or theme from the list
of installed packages.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleIO($input, $output);
        $options = [
            'no-dev' => !$input->getOption('dev'),
            'no-scripts' => !$input->getOption('no-scripts'),
        ];

        $this->initialMessage($io);
        $packageManager = $this->getPackageManager(getcwd(), $io);
        $packageManager->removePackage(
            $input->getArgument('packages'),
            $input->getOption('dev')
        );

        try {
            $packageManager->update($options);
        } catch (\Exception $e) {
            $io->error('Removing failed. Reverting changes...');

            $packageManager->addPackage(
                $input->getArgument('packages'),
                $input->getOption('dev')
            );

            $io->write('Changes reverted successfully!');
            $io->newLine();

            return 1;
        }

        $io->success('Plugins and themes removed');
    }

    protected function initialMessage(ConsoleIO $io)
    {
        $io->newLine(2);
        $io->write('<comment>Removing packages...</comment>');
        $io->newLine();
    }
}
