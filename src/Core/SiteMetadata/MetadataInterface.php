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
 * Iterface for the site metadata. The site metadata is a two level structure
 * composed by sections, keys and values. Each key must be unique in a section.
 * A key has associated a value of type string, integer, boolean or array.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface MetadataInterface
{
    /**
     * Returns the values from the site metadata.
     *
     * @param string $section The name of the section.
     * @param string $key The unique key in the section.
     * @param null|string|int|float|bool|array  $default Default value to return if the key does not exist.
     *
     * @return null|string|int|float|bool|array The value of the key from the site metadata,
     * or $default in case of key miss.
     *
     * @throws \InvalidArgumentException Must be throw if the default value is not a legal value.
     */
    public function get($section, $key, $default = null);

    /**
     * Persists data in the site metadata, uniquely referenced by the pair section-key.
     *
     * @param string $section The name of the section.
     * @param string $key The unique key in the section.
     * @param null|string|int|float|bool|array The value of the key to store.
     *
     * @throws \InvalidArgumentException Must be thrown if the value is not a legal value.
     */
    public function set($section, $key, $value);

    /**
     * Remove a key from a section in the site metadata. If a null value is passed to the
     * key argument the section will be completely removed
     *
     * @param string $section The name of the section.
     * @param string $key The unique key in the section. This value is optional.
     */
    public function remove($section, $key = null);

    /**
     * Loads metadata of the site.
     */
    public function load();

    /**
     * Saves the metadata of the site.
     */
    public function save();

    /**
     * Wipes clean the entire metadata sections with their keys.
     */
    public function clear();
}
