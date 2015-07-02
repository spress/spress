<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core;

use Pimple\Container;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\ConfigLoader\Config;
use Yosymfony\Spress\Core\Configuration\Configuration;
use Yosymfony\Spress\Core\ContentManager\ContentManager;
use Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager;
use Yosymfony\Spress\Core\ContentManager\Collection\CollectionManagerBuilder;
use Yosymfony\Spress\Core\ContentManager\Generator\GeneratorManager;
use Yosymfony\Spress\Core\ContentManager\Permalink\PermalinkGenerator;
use Yosymfony\Spress\Core\ContentManager\Renderizer\TwigRenderizer;
use Yosymfony\Spress\Core\ContentManager\SiteAttribute\SiteAttribute;
use Yosymfony\Spress\Core\DataSource\DataSourceManagerBuilder;
use Yosymfony\Spress\Core\DataWriter\FilesystemDataWriter;
use Yosymfony\Spress\Core\IO\NullIO;
use Yosymfony\Spress\Core\Plugin\PluginManager;

/**
 * Spress application.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Spress extends Container
{
    const VERSION = '2.0.0-DEV';
    const VERSION_ID = '20000';
    const MAJOR_VERSION = '2';
    const MINOR_VERSION = '0';
    const RELEASE_VERSION = '0';
    const EXTRA_VERSION = 'DEV';

    public function __construct()
    {
        parent::__construct();

        $this['spress.version'] = self::VERSION;
        $this['spress.version.details'] = [
            'id' => self::VERSION_ID,
            'major' => self::MAJOR_VERSION,
            'minor' => self::MINOR_VERSION,
            'release' => self::RELEASE_VERSION,
            'extra' => self::EXTRA_VERSION,
        ];

        $this['spress.externals'] = [];

        $this['spress.config.default_filename'] = __DIR__.'/config/default.yml';
        $this['spress.config.build_dir'] = './build';
        $this['spress.config.site_dir'] = './';
        $this['spress.config.env'] = null;
        $this['spress.config.safe'] = null;
        $this['spress.config.drafts'] = null;
        $this['spress.config.timezone'] = null;
        $this['spress.config.values'] = function ($c) {
            $configLoader = new Configuration($c['lib.configLoader'], $c['spress.config.default_filename']);

            return $configLoader->loadConfiguration($c['spress.config.site_dir'], $c['spress.config.env']);
        };

        $this['lib.configLoader'] = function ($c) {
            $locator = new FileLocator([]);

            return new Config([
                new \Yosymfony\ConfigLoader\Loaders\YamlLoader($locator),
                new \Yosymfony\ConfigLoader\Loaders\JsonLoader($locator),
            ]);
        };

        $this['lib.eventDispatcher'] = function ($c) {
            return new EventDispatcher();
        };

        $this['lib.twig.loader_array'] = function ($c) {
            return new \Twig_Loader_Array([]);
        };

        $this['lib.twig.options'] = function ($c) {
            return [
                'autoescape' => false,
                'debug' => $c['spress.config.values']['debug'],
            ];
        };

        $this['lib.twig'] = function ($c) {
            $options = $c['lib.twig.options'];

            $twig = new \Twig_Environment($c['lib.twig.loader_array'], $options);

            if ($options['debug'] === true) {
                $twig->addExtension(new \Twig_Extension_Debug());
            }

            return $twig;
        };

        $this['spress.io'] = function ($c) {
                return new NullIO();
        };

        $this['spress.plugin.classLoader'] = function () {
            $autoloaders = spl_autoload_functions();

            return $autoloaders[0][0];
        };

        $this['spress.plugin.pluginManager'] = function ($c) {
            return new PluginManager($c['lib.eventDispatcher']);
        };

        $this['spress.dataWriter'] = function ($c) {
            $fs = new Filesystem();

            return new FilesystemDataWriter($fs, $c['spress.config.build_dir']);
        };

        $this['spress.dataSourceManager'] = function ($c) {
            $builder = new DataSourceManagerBuilder();
            $dataSources = $c['spress.config.values']['data_sources'];

            return $builder->buildFromConfigArray($dataSources);
        };

        $this['spress.cms.generatorManager'] = function ($c) {
            $generator = new \Yosymfony\Spress\Core\ContentManager\Generator\PaginationGenerator();

            $manager = new GeneratorManager();
            $manager->addGenerator('pagination', $generator);

            return $manager;
        };

        $this['spress.cms.converterManager'] = function ($c) {
            $markdownExts = $c['spress.config.values']['markdown_ext'];

            $cm = new ConverterManager();
            $cm->addConverter(new \Yosymfony\Spress\Core\ContentManager\Converter\MirrorConverter());
            $cm->addConverter(new \Yosymfony\Spress\Core\ContentManager\Converter\MichelfMarkdownConverter($markdownExts));

            return $cm;
        };

        $this['spress.cms.collectionManager'] = function ($c) {
            $builder = new CollectionManagerBuilder();

            return $builder->buildFromConfigArray($c['spress.config.values']['collections']);
        };

        $this['spress.cms.permalinkGenerator'] = function ($c) {
            $permalink = $c['spress.config.values']['permalink'];
            $preservePathTitle = $c['spress.config.values']['preserve_path_title'];

            return new PermalinkGenerator($permalink, $preservePathTitle);
        };

        $this['spress.cms.renderizer'] = function ($c) {
            $twig = $c['lib.twig'];
            $loader = $c['lib.twig.loader_array'];
            $layoutExts = $c['spress.config.values']['layout_ext'];

            return new TwigRenderizer($twig, $loader, $layoutExts);
        };

        $this['spress.cms.siteAttribute'] = function ($c) {
            return new SiteAttribute();
        };

        $this['spress.cms.contentManager'] = $this->factory(function ($c) {
            return new ContentManager(
                $c['spress.dataSourceManager'],
                $c['spress.dataWriter'],
                $c['spress.cms.generatorManager'],
                $c['spress.cms.converterManager'],
                $c['spress.cms.collectionManager'],
                $c['spress.cms.permalinkGenerator'],
                $c['spress.cms.renderizer'],
                $c['spress.cms.siteAttribute'],
                $c['spress.plugin.pluginManager'],
                $c['lib.eventDispatcher']
            );
        });
    }

    /**
     * Parse a site.
     *
     * Example:
     *   $spress['spress.config.site_dir'] = '/my-site-folder';
     *   $spress['spress.config.drafts'] = true;
     *   $spress['spress.config.safe'] = false;
     *   $spress['spress.config.timezone'] = 'UTC';
     *
     *   $spress->parse();
     *
     * @return array
     */
    public function parse()
    {
        $orgDir = getcwd();
        $this->setCurrentDir($this['spress.config.site_dir']);

        $attributes = $this['spress.config.values'];
        $spressAttributes = [];

        if (is_null($this['spress.config.drafts']) === false) {
            $attributes['drafts'] = (bool) $this['spress.config.drafts'];
        }

        if (is_null($this['spress.config.safe']) === false) {
            $attributes['safe'] = (bool) $this['spress.config.safe'];
        }

        if (is_null($this['spress.config.timezone']) === false) {
            $attributes['timezone'] = $this['spress.config.timezone'];
        }

        $result = $this['spress.cms.contentManager']->parseSite(
            $attributes,
            $spressAttributes,
            $attributes['drafts'],
            $attributes['safe'],
            $attributes['timezone']);

        $this->setCurrentDir($orgDir);

        return $result;
    }

    private function setCurrentDir($path)
    {
        if (false === chdir($path)) {
            throw new \RuntomeException(sprintf('Error when change the current dir to "%s"', $path));
        }
    }
}
