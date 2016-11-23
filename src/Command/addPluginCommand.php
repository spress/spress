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
use Yosymfony\Spress\IO\ConsoleIO;

/**
 * Add plugin command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class AddPluginCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDefinition(array(
               new InputArgument('packages', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Packages that should be updated, if not provided all packages are.'),
               new InputOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation from package sources when possible, including VCS information.'),
               new InputOption('dry-run', null, InputOption::VALUE_NONE, 'Outputs the operations but will not execute anything (implicitly enables --verbose).'),
               new InputOption('dev', null, InputOption::VALUE_NONE, 'Enables installation of dev-require packages.'),
               new InputOption('no-scripts', null, InputOption::VALUE_NONE, 'Skips the execution of all scripts defined in composer.json file.'),
           ))
            ->setName('add:plugin')
            ->setDescription('Adds plugins and themes to your composer.json and installs them')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command update the dependencies of your site by the
latest version.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleIO($input, $output);
        $pmOptions = [
            'dry-run' => $input->getOption('dry-run'),
            'prefer-source' => $input->getOption('prefer-source'),
            'no-dev' => !$input->getOption('dev'),
            'no-scripts' => !$input->getOption('no-scripts'),
        ];

        $this->initialMessage($io);
        $packageManager = $this->getPackageManager('./', $io);
        $packageManager->addPackage(
            $input->getArgument('packages'),
            $input->getOption('dev')
        );
        $packageManager->update($pmOptions);

        $this->okMessage($io);
    }

    protected function initialMessage(ConsoleIO $io)
    {
        $io->newLine(2);
        $io->write('<comment>Adding requirements...</comment>');
        $io->newLine();
    }

    protected function okMessage(ConsoleIO $io)
    {
        $io->success('Plugins and themes updated');
    }
}
