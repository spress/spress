<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DataSource;

use Symfony\Component\Config\FileLocator;
use Yosymfony\ConfigLoader\Config;
use Yosymfony\ConfigLoader\Loaders\YamlLoader;
use Yosymfony\ConfigLoader\Loaders\JsonLoader;

/**
 * Attribute parser using Yosymfony\ConfigLoader.
 * syntaxes supported: YAML and JSON
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class AttributeParser implements AttributeParserInterface
{
    const PARSER_YAML = 'yaml';
    const PARSER_JSON = 'json';

    private $config;
    private $type;
    private $pattern = '/^---\r*\n(.*)\r*\n?---\r*\n?/isU';

    /**
     * Constructor
     *
     * @param $type Type of parser: yaml or json.
     */
    public function __construct($type = self::PARSER_YAML)
    {
        $locator = new FileLocator([]);
        $this->config = new Config([
            new YamlLoader($locator),
            new JsonLoader($locator),
        ]);

        switch ($type) {
            case self::PARSER_YAML:
                $this->type = Config::TYPE_YAML;
                break;
            case self::PARSER_JSON:
                $this->type = Config::TYPE_JSON;
                break;
            default:
                throw new \RuntimeException(sprintf('Invalid attributte parser type: "%s".', $type));
        }
    }

    /**
     * @inheritDoc
     */
    public function getAttributesFromString($value)
    {
        $repository = $this->config->load($value, $this->type);

        return $repository->getArray();
    }

    /**
     * @inheritDoc
     */
    public function getAttributesFromFrontmatter($value)
    {
        $found = preg_match($this->pattern, $value, $matches);

        if (1 === $found) {
            return $this->getAttributesFromString($matches[1]);
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function getContentFromFrontmatter($value)
    {
        return ltrim(preg_replace($this->pattern, '', $value, 1));
    }
}
