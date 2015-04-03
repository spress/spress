<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DataSource\Filesystem;

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
class AttributeParser
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
     * Get the attributes of an item from string
     *
     * @return array
     */
    public function getAttributesFromString($value)
    {
        $repository = $this->config->load($value, $this->type);

        return $repository->getArray();
    }

     /**
     * Get the attributes from the fronmatter of an item. Front-matter
     * block let you specify certain attributes of the page and define
     * new variables that will be available in the content.
     *
     * e.g: (YAML syntax)
     *  ---
     *   name: "Victor"
     *  ---
     *
     * @return array Array with two elements: "attributes" and "content".
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
     * Get the content without frontmatter block
     *
     * @return string
     */
    public function getContentFromFrontmatter($value)
    {
        return ltrim(preg_replace($this->pattern, '', $value, 1));
    }
}
