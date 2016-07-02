<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Configuration;

use Yosymfony\ConfigLoader\Config;
use Yosymfony\Spress\Core\Support\AttributesResolver;

/**
 * Configuration loader.
 *
 * Configuration priority:
 *   Default -> config.yml -> config_:env.yml where ":env" is the environment name.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    private $configLoader;
    private $configFilename;
    private $templateEnvFilename;
    private $defaultConfigFilename;

    private $resolver;
    private $defaultConfiguration = [];

    /**
     * Constructor. By default the configuration filename is "config.yml"
     * and the template for environment filename is "config_:env.yml".
     *
     * @param \Yosymfony\ConfigLoader\Config $configLoader
     * @param string                         $defaultConfigFilename Path to filename with defatul configuration.
     */
    public function __construct(Config $configLoader, $defaultConfigFilename)
    {
        $this->configLoader = $configLoader;
        $this->defaultConfigFilename = $defaultConfigFilename;

        $this->configFilename = 'config.yml';
        $this->templateEnvFilename = 'config_:env.yml';

        $this->resolver = $this->getConfigurationResolver();
    }

    /**
     * Sets the configuration filename. e.g: "config.yml".
     *
     * @param string $filename
     */
    public function setConfigFilename($filename)
    {
        $this->configFilename = $filename;
    }

    /**
     * Sets the template for filename related with environment configuration.
     * e.g: "config_:env.yml".
     *
     * Placeholders:
     *  - ":env" will be replaced by the environment's name.
     *
     * @param string $templateFilename
     */
    public function setTemplateEnvFilename($templateFilename)
    {
        $this->templateEnvFilename = $templateFilename;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration($sitePath, $envName = null)
    {
        if ($this->isSpressSite($sitePath) === false) {
            throw new \RuntimeException(sprintf('Not a Spress site at "%s".', $sitePath));
        }

        $default = $this->loadDefaultConfiguration();
        $dev = $this->loadEnvironmentConfiguration($sitePath, 'dev');
        $result = $this->resolver->resolve(array_merge($default, $dev));

        if (is_null($envName)) {
            $envName = $result['env'];
        }

        if ($envName !== 'dev') {
            $environment = $this->loadEnvironmentConfiguration($sitePath, $envName);
            $environment['env'] = $envName;
            $result = $this->resolver->resolve(array_merge($result, $environment));
        }

        return $result;
    }

    private function loadEnvironmentConfiguration($sitePath, $env)
    {
        $filename = $this->getConfigEnvFilename($env);
        $repository = $this->configLoader->load($sitePath.'/'.$filename);

        return $repository->getArray();
    }

    private function loadDefaultConfiguration()
    {
        if (isset($this->defaultConfiguration)) {
            $repository = $this->configLoader->load($this->defaultConfigFilename);
            $this->defaultConfiguration = $repository->getArray();
        }

        return $this->resolver->resolve($this->defaultConfiguration);
    }

    private function getConfigEnvFilename($env)
    {
        if (empty($env)) {
            throw new \RuntimeException('Expected a non-empty string as environment name.');
        }

        if (strtolower($env) === 'dev') {
            return $this->configFilename;
        }

        $filename = str_replace(':env', $env, $this->templateEnvFilename);

        return $filename;
    }

    private function isSpressSite($sitePath)
    {
        return file_exists($sitePath.'/'.$this->configFilename);
    }

    private function getConfigurationResolver()
    {
        $resolver = new AttributesResolver();
        $resolver->setDefault('debug', false, 'bool', true)
            ->setDefault('env', 'dev', 'string', true)
            ->setValidator('env', function ($value) {
                return strlen($value) > 0;
            })
            ->setDefault('drafts', false, 'bool', true)
            ->setDefault('preserve_path_title', false, 'bool', true)
            ->setDefault('no_html_extension', false, 'bool', true)
            ->setDefault('timezone', 'UTC', 'string', true)
            ->setDefault('url', '', 'string', true)
            ->setDefault('safe', false, 'bool', true)
            ->setDefault('layout_ext', [], 'array', true)
            ->setDefault('data_sources', [], 'array', true)
            ->setDefault('collections', [], 'array')
            ->setDefault('permalink', 'pretty', 'string', true)
            ->setDefault('markdown_ext', [], 'array', true)
            ->setDefault('plugin_manager_builder', [], 'array', true)
            ->setValidator('plugin_manager_builder', function ($value) {
                return isset($value['exclude_path']) && is_array($value['exclude_path']);
            });

        return $resolver;
    }
}
