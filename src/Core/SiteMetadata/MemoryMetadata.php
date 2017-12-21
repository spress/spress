<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\SiteMetadata;

/**
 * Metadata site in memory implementation
 */
class MemoryMetadata implements MetadataInterface
{
    protected $metadata = [];

    /**
     * {@inheritdoc}
     */
    public function get($section, $key, $default = null)
    {
        $this->checksValidTypes($default);

        if (isset($this->metadata[$section]) === true && array_key_exists($key, $this->metadata[$section])) {
            return $this->metadata[$section][$key];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($section, $key, $value)
    {
        $this->checksValidTypes($value);

        if (isset($this->metadata[$section]) === false) {
            $this->metadata[$section] = [];
        }

        $this->metadata[$section][$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($section, $key = null)
    {
        if ($key === null) {
            $this->metadata[$section] = [];
        }

        if (isset($this->metadata[$section]) === true) {
            unset($this->metadata[$section][$key]);
        }

        if (count($this->metadata[$section]) === 0) {
            unset($this->metadata[$section]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->metadata = [];
    }

    /**
     * @param string $value
     *
     * @return void
     */
    protected function checksValidTypes($value)
    {
        if (is_object($value) === true) {
            $message = "The value or default value must be a valid type: string, integer, float, boolean or array.";
            throw new \InvalidArgumentException($message);
        }
    }
}
