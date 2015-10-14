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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Yosymfony\Spress\Core\Spress;

/**
 * Welcome to Spress command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class WelcomeCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('welcome')
            ->setDescription('Welcome to Spress message');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '',
            $this->getSpressAsciiArt(),
            '',
            'Hola user and welcome!',
            '',
            'More information at <comment>http://spress.yosymfony.com</comment> or <comment>@spress_cms</comment> on Twitter',
            '',
        ]);

        if ($this->isUnstableVersion()) {
            $output->writeln([
                '',
                '<error>Warning: this is a unstable version.</error>',
                ``,
            ]);
        }

        $command = $this->getApplication()->find('list');

        $arguments = new ArrayInput([
            'command' => 'list',
        ]);
        $command->run($arguments, $output);
    }

    protected function getSpressAsciiArt()
    {
        return <<<EOF
             (
     )\ )
    (()/(            (      (
     /(_))    `  )   )(    ))\ (   (
 __ (_))  __  /(/(  (()\  /((_))\  )\
| _|/ __||_ |((_)_\  ((_)(_)) ((_)((_)
| | \__ \ | || '_ \)| '_|/ -_)(_-<(_-<
| | |___/ | || .__/ |_|  \___|/__//__/
|__|     |__||_|
EOF;
    }

    protected function isUnstableVersion()
    {
        return Spress::EXTRA_VERSION ? true : false;
    }
}
