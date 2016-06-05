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

use Dflydev\EmbeddedComposer\Core\EmbeddedComposerBuilder;
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
use Yosymfony\Spress\Core\Plugin\PluginManagerBuilder;

/**
 * The Spress application.
 *
 * Namespaces:
 *  - "spress.config.site_dir": (string) The path to the site. "./" by defatul.
 *
 *  - "spress.externals": (array) Externals attributes. e.g: CLI command arguments.
 *    These attributes could be recovered on a site using "spress.external.attribute_name".
 *
 *  - "spress.dataSourceManager.parameters": (array) Parameters accesibles by arguments
 *    at Datasources declaration.
 *
 *  - "spress.cms.converterManager.converters" (array) List of predefined converters.
 *
 * @api
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Spress extends Container
{
    const VERSION = '2.1.2';
    const VERSION_ID = '20102';
    const MAJOR_VERSION = '2';
    const MINOR_VERSION = '1';
    const RELEASE_VERSION = '2';
    const EXTRA_VERSION = '';

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
        $this['spress.config.site_dir'] = realpath('./');

        $this['spress.config.build_dir'] = function ($c) {
            return $c['spress.config.site_dir'].'/build';
        };
        $this['spress.config.plugin_dir'] = function ($c) {
            return $c['spress.config.site_dir'].'/src/plugins';
        };

        $this['spress.config.vendor_dir'] = function ($c) {
            return 'vendor';
        };

        $this['spress.config.composer_filename'] = 'composer.json';
        $this['spress.config.env'] = null;
        $this['spress.config.safe'] = null;
        $this['spress.config.drafts'] = null;
        $this['spress.config.url'] = null;
        $this['spress.config.timezone'] = null;
        $this['spress.config.values'] = function ($c) {
            $configLoader = $c['spress.config'];

            $attributes = $configLoader->loadConfiguration($c['spress.config.site_dir'], $c['spress.config.env']);

            if (is_null($this['spress.config.drafts']) === false) {
                $attributes['drafts'] = (bool) $this['spress.config.drafts'];
            }

            if (is_null($this['spress.config.safe']) === false) {
                $attributes['safe'] = (bool) $this['spress.config.safe'];
            }

            if (is_null($this['spress.config.timezone']) === false) {
                $attributes['timezone'] = $this['spress.config.timezone'];
            }

            if (is_null($this['spress.config.url']) === false) {
                $attributes['url'] = $this['spress.config.url'];
            }

            return $attributes;
        };

        $this['spress.config'] = function ($c) {
            return new Configuration($c['lib.configLoader'], $c['spress.config.default_filename']);
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

        $this['lib.embeddedComposer'] = function ($c) {
            $embeddedComposerBuilder = new EmbeddedComposerBuilder($c['spress.plugin.classLoader'], $c['spress.config.site_dir']);
            $embeddedComposer = $embeddedComposerBuilder
                ->setComposerFilename($c['spress.config.composer_filename'])
                ->setVendorDirectory($c['spress.config.vendor_dir'])
                ->build();

            return $embeddedComposer;
        };

        $this['spress.io'] = function ($c) {
            return new NullIO();
        };

        $this['spress.plugin.classLoader'] = function ($c) {
            $autoloaders = spl_autoload_functions();

            return $autoloaders[0][0];
        };

        $this['spress.plugin.pluginManager'] = function ($c) {
            $pathComposerFilename = $c['spress.config.site_dir'].'/'.$c['spress.config.composer_filename'];

            if (file_exists($pathComposerFilename) === true) {
                $embeddedComposer = $c['lib.embeddedComposer'];
                $embeddedComposer->processAdditionalAutoloads();
            }

            $builder = new PluginManagerBuilder($c['spress.config.plugin_dir'], $c['lib.eventDispatcher']);

            return $builder->build();
        };

        $this['spress.dataWriter'] = function ($c) {
            $fs = new Filesystem();

            return new FilesystemDataWriter($fs, $c['spress.config.build_dir']);
        };

        $this['spress.dataSourceManager.parameters'] = function ($c) {
            return [
                '%site_dir%' => $c['spress.config.site_dir'],
                '%include%' => $c['spress.config.values']['include'],
                '%exclude%' => $c['spress.config.values']['exclude'],
                '%text_extensions%' => $c['spress.config.values']['text_extensions'],
                '%attribute_syntax%' => $c['spress.config.values']['attribute_syntax'],
            ];
        };

        $this['spress.dataSourceManager'] = function ($c) {
            $parameters = $c['spress.dataSourceManager.parameters'];
            $builder = new DataSourceManagerBuilder($parameters);
            $dataSources = $c['spress.config.values']['data_sources'];

            return $builder->buildFromConfigArray($dataSources);
        };

        $this['spress.cms.generatorManager'] = function ($c) {
            $pagination = new \Yosymfony\Spress\Core\ContentManager\Generator\Pagination\PaginationGenerator();
            $taxonomy = new \Yosymfony\Spress\Core\ContentManager\Generator\Taxonomy\TaxonomyGenerator();

            $manager = new GeneratorManager();
            $manager->addGenerator('pagination', $pagination);
            $manager->addGenerator('taxonomy', $taxonomy);

            return $manager;
        };

        $this['spress.cms.converterManager.converters'] = function ($c) {
            $markdownExts = $c['spress.config.values']['markdown_ext'];
            $extensionMappingTable = $c['spress.config.values']['map_converter_extension'];

            return [
                'MapConverter' => new \Yosymfony\Spress\Core\ContentManager\Converter\MapConverter($extensionMappingTable),
                'MichelfMarkdownConverter' => new \Yosymfony\Spress\Core\ContentManager\Converter\MichelfMarkdownConverter($markdownExts),
            ];
        };

        $this['spress.cms.converterManager'] = function ($c) {
            $cm = new ConverterManager($c['spress.config.values']['text_extensions']);
            $converters = $c['spress.cms.converterManager.converters'];

            foreach ($converters as $converter) {
                $cm->addConverter($converter);
            }

            return $cm;
        };

        $this['spress.cms.collectionManager'] = function ($c) {
            $builder = new CollectionManagerBuilder();

            return $builder->buildFromConfigArray($c['spress.config.values']['collections']);
        };

        $this['spress.cms.permalinkGenerator'] = function ($c) {
            $permalink = $c['spress.config.values']['permalink'];
            $preservePathTitle = $c['spress.config.values']['preserve_path_title'];
            $noHtmlExtension = $c['spress.config.values']['no_html_extension'];

            return new PermalinkGenerator($permalink, $preservePathTitle, $noHtmlExtension);
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
                $c['lib.eventDispatcher'],
                $c['spress.io']
            );
        });
    }

    /**
     * Parse a site.
     *
     * Example:
     *   $spress['spress.config.site_dir'] = '/my-site-folder';
     *
     *   $spress['spress.config.drafts'] = true;
     *   $spress['spress.config.safe'] = false;
     *   $spress['spress.config.timezone'] = 'UTC';
     *   $spress['spress.config.url'] = 'http://your-domain.local:4000';
     *
     *   $spress->parse();
     *
     * @return \Yosymfony\Spress\Core\DataSource\ItemInterface[] Items of the site.
     */
    public function parse()
    {
        $attributes = $this['spress.config.values'];
        $spressAttributes = $this->getSpressAttributes();

        $result = $this['spress.cms.contentManager']->parseSite(
            $attributes,
            $spressAttributes,
            $attributes['drafts'],
            $attributes['safe'],
            $attributes['timezone']);

        return $result;
    }

    private function getSpressAttributes()
    {
        return [
            'version' => $this['spress.version'],
            'version_id' => $this['spress.version.details']['id'],
            'major_version' => $this['spress.version.details']['major'],
            'minor_version' => $this['spress.version.details']['minor'],
            'release_version' => $this['spress.version.details']['release'],
            'extra_version' => $this['spress.version.details']['extra'],
            'external' => $this['spress.externals'],
        ];
    }
}
