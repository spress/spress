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
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\ConfigLoader\Config;
use Yosymfony\Spress\Core\Configuration\Configuration;
use Yosymfony\Spress\Core\ContentManager\ContentManager;
use Yosymfony\Spress\Core\ContentManager\Converter\ConverterManager;
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
    const VERSION = "2.0.0-DEV";
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


        $this['lib.configLoader'] = function ($app) {
            $locator = new FileLocator([]);

            return new Config([
                new \Yosymfony\ConfigLoader\Loaders\YamlLoader($locator),
                new \Yosymfony\ConfigLoader\Loaders\JsonLoader($locator),
            ]);
        };

        $this['lib.eventDispatcher'] = function ($c) {
            return new EventDispatcher();
        }

        $this['spress.configuration'] = function ($c) {
            return new Configuration($c['lib.configLoader'], $c['spress.config.default_filename']);
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
        }

        $this['spress.dataWriter'] = function ($c) {
            $fs = new Filesystem();

            return new FilesystemDataWriter($c['spress.config.build_dir']);
        }

        $this['spress.dataSourceManager'] = function ($c) {
            
        }

        $this['spress.cms.generatorManager'] = function ($c) {
            
        }

        $this['spress.cms.converterManager'] = function ($c) {
            
        }

        $this['spress.cms.collectionManager'] = function ($c) {
            
        }

        $this['spress.cms.permalinkGenerator'] = function ($c) {
            
        }

        $this['spress.cms.renderizer'] = function ($c) {
            
        }

        $this['spress.cms.siteAttribute'] = function ($app) {
            
        }
    }

    /**
     * Parse a site
     *
     * @param string $localConfigPath Path of the local configuration
     * @param string $env             Environment name
     * @param string $timezone        Set the timezone
     * @param bool   $drafts          Include draft
     * @param bool   $safe            Plugins disabled
     * @param string $url             URL base
     *
     * @return array Key-value result
     */
    public function parse($localConfigPath = null, $env = null, $timezone = null, $drafts = null, $safe = null, $url = null)
    {
       return [];
    }
}
