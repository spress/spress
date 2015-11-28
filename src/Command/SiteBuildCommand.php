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
use Yosymfony\Spress\Converter\ParsedownConverter;
use Yosymfony\Spress\IO\ConsoleIO;
use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\Core\Spress;
use Yosymfony\Spress\Core\Support\AttributesResolver;
use Yosymfony\Spress\HttpServer\HttpServer;

/**
 * Build command.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class SiteBuildCommand extends Command
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

                $server->onBeforeRequest(function ($request, $resourcePath, $io) use ($io, $input, $rw, $serverWatchExtension) {
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

    /**
     * Buils a Spress instance.
     *
     * @param \Yosymfony\Spress\Core\IO\IOInterface           $io
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \Yosymfony\Spress\Core\Spress
     */
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

        $resolver = $this->getConfigResolver();
        $resolver->resolve($spress['spress.config.values']);

        if ($spress['spress.config.values']['parsedown_activated'] === true) {
            $this->enableParsedown($spress);

            $io->write('<comment>Parsedown converter: enabled.</comment>');
        }

        $spress['spress.io'] = $io;

        return $spress;
    }

    /**
     * Reparse a site.
     *
     * @param \Yosymfony\Spress\Core\IO\IOInterface           $io
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Yosymfony\ResourceWatcher\ResourceWatcher      $rw
     */
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

        $io->write('<success>Site ready.</success>');
    }

    /**
     * Builds a ResourceWatcher instance.
     *
     * @param string $sourceDir      Source path.
     * @param string $destinationDir Destination path.
     *
     * @return \Yosymfony\ResourceWatcher\ResourceWatcher
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
     * Writes the staring messages.
     *
     * @param \Yosymfony\Spress\IO\ConsoleIO $io
     * @param string                         $env    [description]
     * @param bool                           $drafts [description]
     * @param bool                           $safe   [description]
     */
    protected function startingMessage(ConsoleIO $io, $env, $drafts, $safe)
    {
        $io->write([
            '',
            '<info>Starting...</info>',
        ]);
        $io->write(sprintf('<info>Environment: %s.</info>', $env));

        if ($io->isDebug()) {
            $io->write('<info>Debug mode enabled.</info>');
        }

        if ($drafts) {
            $io->write('<info>Posts drafts enabled.</info>');
        }

        if ($safe) {
            $io->write('<info>Plugins disabled.</info>');
        }
    }

    /**
     * Writes the result of a parsing a site.
     *
     * @param \Yosymfony\Spress\IO\ConsoleIO                    $io
     * @param \Yosymfony\Spress\Core\DataSource\ItemInterface[] $items
     */
    protected function resultMessage(ConsoleIO $io, array $items)
    {
        $io->write(sprintf('<info>Total items: %d.</info>', count($items)));
    }

    /**
     * Gets the attributes or option resolver for configuration values.
     *
     * @return \Yosymfony\Spress\Core\Support\AttributesResolver
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
     * @param Spress $spress
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
}
