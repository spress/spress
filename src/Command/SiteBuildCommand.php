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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\ResourceWatcher\ResourceWatcher;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;
use Yosymfony\Spress\IO\ConsoleIO;
use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\Core\Spress;
use Yosymfony\Spress\HttpServer\HttpServer;

/**
 * Build command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SiteBuildCommand extends Command
{
    protected function configure()
    {
        $this->setDefinition([
            new InputOption('source', 's', InputOption::VALUE_REQUIRED, 'Directory where Spress will read your files'),
            new InputOption('timezone', null, InputOption::VALUE_REQUIRED, 'Timezone for the site generator'),
            new InputOption('env', null, InputOption::VALUE_REQUIRED, 'Name of the environment configuration'),
            new InputOption('watch', null, InputOption::VALUE_NONE, 'Watching for changes and regenerate automatically your site'),
            new InputOption('server', null, InputOption::VALUE_NONE, 'Start the built-in server'),
            new InputOption('drafts', null, InputOption::VALUE_NONE, 'Parse your draft posts'),
            new InputOption('safe', null, InputOption::VALUE_NONE, 'Disable your template plugins'),
        ])
        ->setName('site:build')
        ->setDescription('Build your site');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = $input->getOption('server');
        $watch = $input->getOption('watch');

        $io = new ConsoleIO($input, $output, $this->getHelperSet());

        $spress = $this->buildSpress($io, $input);

        $env = $spress['spress.config.values']['env'];
        $drafts = $spress['spress.config.values']['drafts'];
        $safe = $spress['spress.config.values']['safe'];
        $debug = $spress['spress.config.values']['debug'];
        $host = $spress['spress.config.values']['host'];
        $port = $spress['spress.config.values']['port'];
        $serverWatchExtension = $spress['spress.config.values']['server_watch_ext'];

        if ($debug === true) {
            $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        $this->startingMessage($io, $env, $drafts, $safe);

        $resultData = $spress->parse();

        $this->resultMessage($io, $resultData);

        $sourceDir = $spress['spress.config.site_dir'];
        $destinationDir = $spress['spress.config.build_dir'];
        $rw = $this->buildResourceWatcher($sourceDir, $destinationDir);

        if ($server === true) {
            $documentroot = $destinationDir;
            $serverroot = __DIR__.'/../../app/httpServer';

            $server = new HttpServer($io, $serverroot, $documentroot, $port, $host);

            if ($watch === true) {
                $io->write('<comment>Auto-regeneration: enabled.</comment>');

                $server->onBeforeHandleRequest(function ($request, $resourcePath, $io) use ($io, $input, $rw, $serverWatchExtension) {
                    $resourceExtension = pathinfo($resourcePath, PATHINFO_EXTENSION);

                    if (in_array($resourceExtension, $serverWatchExtension)) {
                        $this->reParse($io, $input, $rw);
                    }
                });
            }

            $server->start();
        } elseif ($watch) {
            $io->write('<comment>Auto-regeneration: enabled. Press ctrl-c to stop.</comment>');

            do {
                sleep(2);

                $this->reParse($io, $input, $rw);
            } while (true);
        }
    }

    protected function buildSpress(IOInterface $io, InputInterface $input)
    {
        $timezone = $input->getOption('timezone');
        $drafts = $input->getOption('drafts');
        $safe = $input->getOption('safe');
        $env = $input->getOption('env');
        $sourceDir = $input->getOption('source');

        $spress = new Spress();
        $spress['spress.config.default_filename'] = __DIR__.'/../../app/config/config.yml';

        if (is_null($sourceDir) === false) {
            if (($realDir = realpath($sourceDir)) === false) {
                throw new \RuntimeException(sprintf('Invalid source path: "%s".', $sourceDir));
            }

            $spress['spress.config.site_dir'] = $realDir;
        }

        $spress['spress.config.env'] = $env;
        $spress['spress.config.safe'] = $safe;
        $spress['spress.config.drafts'] = $drafts;
        $spress['spress.config.timezone'] = $timezone;

        return $spress;
    }

    protected function reParse(IOInterface $io, InputInterface $input, ResourceWatcher $rw)
    {
        $rw->findChanges();

        if ($rw->hasChanges() === false) {
            return;
        }

        $io->write(sprintf(
            '<comment>Rebuilding site... (%s new, %s updated and %s deleted resources)</comment>',
            count($rw->getNewResources()),
            count($rw->getUpdatedResources()),
            count($rw->getDeletedResources())));

        $spress = $this->buildSpress($io, $input);
        $spress->parse();

        $io->write('<comment>Site ready.</comment>');
    }

    protected function buildResourceWatcher($sourceDir, $destinationDir)
    {
        $fs = new Filesystem();
        $relativeDestination = rtrim($fs->makePathRelative($destinationDir, $sourceDir), '/');

        $finder = new Finder();
        $finder->files()
            ->name('*.*')
            ->in($sourceDir);

        if (false === strpos($relativeDestination, '..')) {
            $finder->exclude($relativeDestination);
        }

        $rc = new ResourceCacheMemory();
        $rw = new ResourceWatcher($rc);
        $rw->setFinder($finder);

        return $rw;
    }

    protected function startingMessage(ConsoleIO $io, $env, $drafts, $safe)
    {
        $io->write('<comment>Starting...</comment>');
        $io->write(sprintf('<comment>Environment: %s.</comment>', $env));

        if ($io->isDebug()) {
            $io->write('<comment>Debug mode enabled.</comment>');
        }

        if ($drafts) {
            $io->write('<comment>Posts drafts enabled.</comment>');
        }

        if ($safe) {
            $io->write('<comment>Plugins disabled.</comment>');
        }
    }

    protected function resultMessage(ConsoleIO $io, array $items)
    {
        $io->write(sprintf('Total items: %d', count($items)));
    }
}
