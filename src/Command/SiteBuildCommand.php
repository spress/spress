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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\ResourceWatcher\ResourceWatcher;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;
use Yosymfony\Spress\Converter\ParsedownConverter;
use Yosymfony\Spress\IO\ConsoleIO;
use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\Core\Spress;
use Yosymfony\Spress\Core\Support\AttributesResolver;
use Yosymfony\Spress\HttpServer\HttpServer;
use Yosymfony\Spress\HttpServer\ServerRequest;

/**
 * Build command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SiteBuildCommand extends BaseCommand
{
    protected $configResolver;

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = $input->getOption('server');
        $watch = $input->getOption('watch');

        $io = new ConsoleIO($input, $output);

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
                $io->labelValue('Auto-regeneration', 'enabled');

                $server->onBeforeRequest(function (ServerRequest $request) use ($io, $input, $rw, $serverWatchExtension) {
                    $resourceExtension = pathinfo($request->getPathFilename(), PATHINFO_EXTENSION);

                    if (in_array($resourceExtension, $serverWatchExtension)) {
                        $this->reParse($io, $input, $rw);
                    }
                });
            }

            $server->start();
        } elseif ($watch) {
            $io->labelValue('Auto-regeneration', 'enabled');
            $io->write('<comment>Press ctrl-c to stop.</comment>');
            $io->newLine();

            do {
                sleep(2);

                $this->reParse($io, $input, $rw);
            } while (true);
        }
    }

    /**
     * Builds a Spress instance.
     *
     * @param IOInterface    $io    The Spress IO
     * @param InputInterface $input The Symfony Console input
     *
     * @return Spress The Spress instance
     */
    protected function buildSpress(IOInterface $io, InputInterface $input)
    {
        $timezone = $input->getOption('timezone');
        $drafts = $input->getOption('drafts');
        $safe = $input->getOption('safe');
        $env = $input->getOption('env');
        $sourceDir = $input->getOption('source');

        if (is_null($sourceDir) === true) {
            $sourceDir = './';
        }

        if (($realDir = realpath($sourceDir)) === false) {
            throw new \RuntimeException(sprintf('Invalid source path: "%s".', $sourceDir));
        }

        $spress = $this->getSpress($realDir);

        $spress['spress.config.env'] = $env;
        $spress['spress.config.safe'] = $safe;
        $spress['spress.config.drafts'] = $drafts;
        $spress['spress.config.timezone'] = $timezone;

        $resolver = $this->getConfigResolver();
        $resolver->resolve($spress['spress.config.values']);

        if ($spress['spress.config.values']['parsedown_activated'] === true) {
            $this->enableParsedown($spress);

            $io->labelValue('Parsedown converter', 'enabled');
        }

        $spress['spress.io'] = $io;

        return $spress;
    }

    /**
     * Reparses a site.
     *
     * @param IOInterface     $io    The Spress IO
     * @param InputInterface  $input The Symfony Console input
     * @param ResourceWatcher $rw    The ResourceWatcher
     */
    protected function reParse(IOInterface $io, InputInterface $input, ResourceWatcher $rw)
    {
        $rw->findChanges();

        if ($rw->hasChanges() === false) {
            return;
        }

        $this->rebuildingSiteMessage($io, $rw->getNewResources(), $rw->getUpdatedResources(), $rw->getDeletedResources());

        $spress = $this->buildSpress($io, $input);
        $resultData = $spress->parse();

        $this->resultMessage($io, $resultData);
    }

    /**
     * Builds a ResourceWatcher instance.
     *
     * @param string $sourceDir      Source path
     * @param string $destinationDir Destination path
     *
     * @return ResourceWatcher The ResourceWatcher instance
     */
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

    /**
     * Returns the attributes or options resolver for configuration values.
     *
     * @return AttributesResolver
     */
    protected function getConfigResolver()
    {
        if (is_null($this->configResolver) === false) {
            return $this->configResolver;
        }

        $resolver = new AttributesResolver();
        $resolver->setDefault('host', '0.0.0.0', 'string', true)
            ->setDefault('port', 4000, 'integer', true)
            ->setValidator('port', function ($value) {
                return $value >= 0;
            })
            ->setDefault('server_watch_ext', ['html'], 'array', true)
            ->setDefault('parsedown_activated', false, 'bool', true);

        $this->configResolver = $resolver;

        return $this->configResolver;
    }

    /**
     * Enables Parsedown converter.
     *
     * @param Spress $spress The Spress instance
     */
    protected function enableParsedown(Spress $spress)
    {
        $spress->extend('spress.cms.converterManager.converters', function ($predefinedConverters, $c) {
            unset($predefinedConverters['MichelfMarkdownConverter']);

            $markdownExts = $c['spress.config.values']['markdown_ext'];
            $predefinedConverters['ParsedownConverter'] = new ParsedownConverter($markdownExts);

            return $predefinedConverters;
        });
    }

    /**
     * Writes the staring messages.
     *
     * @param ConsoleIO $io    Spress IO
     * @param string    $env   The enviroment name
     * @param bool      $draft Is draft mode enabled?
     * @param bool      $safe  Is safe mode enabled?
     */
    protected function startingMessage(ConsoleIO $io, $env, $draft, $safe)
    {
        $io->newLine();
        $io->write('<comment>Starting...</comment>');
        $io->labelValue('Environment', $env);

        if ($io->isDebug() === true) {
            $io->labelValue('Debug mode', 'enabled');
        }

        if ($draft === true) {
            $io->labelValue('Draft posts', 'enabled');
        }

        if ($safe === true) {
            $io->labelValue('Plugins', 'disabled');
        }
    }

    /**
     * Writes the result of a parsing a site.
     *
     * @param ConsoleIO       $io    The Spress IO
     * @param ItemInterface[] $items Item affected
     */
    protected function resultMessage(ConsoleIO $io, array $items)
    {
        $io->newLine();
        $io->labelValue('Total items', count($items));
        $io->newLine();
        $io->success('Success!');
    }

    /**
     * Write the result of rebuilding a site.
     *
     * @param ConsoleIO $io               The Spress IO
     * @param array     $newResources
     * @param array     $updatedResources
     * @param array     $deletedResources
     */
    protected function rebuildingSiteMessage(ConsoleIO $io, array $newResources, array $updatedResources, array $deletedResources)
    {
        $io->write(sprintf(
            '<comment>Rebuilding site... (%s new, %s updated and %s deleted resources)</comment>',
            count($newResources),
            count($updatedResources),
            count($deletedResources)));
        $io->newLine();
    }
}
