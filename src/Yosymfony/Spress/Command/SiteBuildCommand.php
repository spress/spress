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
use Yosymfony\Spress\HttpServer\HttpServer;

/**
 * Build command
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
        $timezone = $input->getOption('timezone');
        $drafts = $input->getOption('drafts');
        $safe = $input->getOption('safe');
        $env = $input->getOption('env');
        $server = $input->getOption('server');
        $watch = $input->getOption('watch');
        $sourceDir = $input->getOption('source');

        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        $app = new SpressCLI($io);

        $config = $app['spress.config'];
        $config->loadLocal($sourceDir, $env);
        $env = $config->getEnvironmentName();
        $serverWatchExtension = $config->getRepository()->get('server_watch_ext');

        if (true === $config->getRepository()->get('debug')) {
            $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        $parse = function () use (&$app, $env, $timezone, $drafts, $safe) {
            return $app->parseDefault(
                $env,
                $timezone,
                $drafts,
                $safe,
                null);
        };

        $this->startingMessage($io, $env, $drafts, $safe);
        $resultData = $parse();

        $this->resultMessage($io, $resultData);

        $contentLocator = $app['spress.content_locator'];
        $rw = $this->buildResourceWatcher($contentLocator->getSourceDir(), $contentLocator->getDestinationDir());

        $findChangesAndParse = function () use (&$io, &$rw, &$parse) {
            $rw->findChanges();

            if ($rw->hasChanges()) {
                $io->write(sprintf(
                    '<comment>Rebuilding site... (%s new, %s updated and %s deleted resources)</comment>',
                    count($rw->getNewResources()),
                    count($rw->getUpdatedResources()),
                    count($rw->getDeletedResources())));

                $parse();

                $io->write('<comment>Site ready.</comment>');
            }
        };

        if ($server) {
            $port = $config->getRepository()->get('port');
            $host = $config->getRepository()->get('host');
            $documentroot = $contentLocator->getDestinationDir();
            $serverroot = $app['spress.paths']['http_server_root'];

            $server = new HttpServer($io, $serverroot, $documentroot, $port, $host);

            if ($watch) {
                $io->write('<comment>Auto-regeneration: enabled.</comment>');

                $server->onBeforeHandleRequest(function ($request, $resourcePath, $io) use ($findChangesAndParse, $serverWatchExtension) {
                    $resourceExtension = pathinfo($resourcePath, PATHINFO_EXTENSION);

                    if (in_array($resourceExtension, $serverWatchExtension)) {
                        $findChangesAndParse();
                    }
                });
            }

            $server->start();
        } elseif ($watch) {
            $io->write('<comment>Auto-regeneration: enabled. Press ctrl-c to stop.</comment>');

            do {
                sleep(2);

                $findChangesAndParse();
            } while (true);
        }
    }

    private function buildResourceWatcher($sourceDir, $destinationDir)
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

    private function startingMessage(ConsoleIO $io, $env, $drafts, $safe)
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

    private function resultMessage(ConsoleIO $io, array $resultData)
    {
        $io->write(sprintf('Total posts: %d', $resultData['total_post']));
        $io->write(sprintf('Processed posts: %d', $resultData['processed_post']));
        $io->write(sprintf('Drafts post: %d', $resultData['drafts_post']));
        $io->write(sprintf('Total pages: %d', $resultData['total_pages']));
        $io->write(sprintf('Processed pages: %d', $resultData['processed_pages']));
        $io->write(sprintf('Other resources: %d', $resultData['other_resources']));
    }
}
