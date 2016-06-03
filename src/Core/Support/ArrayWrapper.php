<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Support;

/**
 * A wrapper for working with arrays.
 *
 * Based on https://github.com/laravel/framework/blob/5.0/src/Illuminate/Support/Arr.php
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ArrayWrapper
{
    protected $array;

    /**
     * Constructor.
     *
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    /**
     * Adds an element using "dot" notation if doesn't exists.
     * You can to escape a dot in a key surrendering with brackets: "[.]".
     *
     * @param $key
     * @param $value
     *
     * @return array
     */
    public function add($key, $value)
    {
        if ($this->has($key) === false) {
            $this->set($key, $value);
        }

        return $this->array;
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @return array A single level array.
     */
    public function flatten()
    {
        $array = [];

        array_walk_recursive($this->array, function ($element) use (&$array) {
            $array[] = $element;
        });

        return $array;
    }

    /**
     * Gets a value from a deeply nested array using "dot" notation.
     * You can to escape a dot in a key surrendering with brackets: "[.]".
     *
     * e.g: $a->get('site.data') or $a->get('site.pages.index[.]html')
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $array = $this->array;
        $unescapedKey = $this->unescapeDotKey($key);

        if (isset($array[$unescapedKey])) {
            return $array[$unescapedKey];
        }

        foreach (explode('.', $this->escapedDotKeyToUnderscore($key)) as $segment) {
            $segment = $this->underscoreDotKeyToDot($segment);

            if (is_array($array) === false || array_key_exists($segment, $array) === false) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Gets the working array.
     *
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * Checks if an item exists in using "dot" notation.
     * You can to escape a dot in a key surrendering with brackets: "[.]".
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        $array = $this->array;

        if (empty($array) || is_null($key)) {
            return false;
        }

        if (array_key_exists($this->unescapeDotKey($key), $array)) {
            return true;
        }

        foreach (explode('.', $this->escapedDotKeyToUnderscore($key)) as $segment) {
            $segment = $this->underscoreDotKeyToDot($segment);

            if (is_array($array) === false || array_key_exists($segment, $array) === false) {
                return false;
            }

            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Paginates the array.
     *
     * @param int    $maxPerPage  Max items per page. If this value is minor than 1 the result will be an empty array.
     * @param int    $initialPage Initial page. Page 1 by default.
     * @param string $key         Element to paginate using "dot" notation.
     *
     * @return array A list of pages with an array of elements associated with each page
     */
    public function paginate($maxPerPage, $initialPage = 1, $key = null)
    {
        $result = [];
        $page = $initialPage;
        $array = $this->array;

        if ($maxPerPage <= 0) {
            return $result;
        }

        if (is_null($key) === false) {
            $array = $this->get($key);
        }

        $arrCount = count($array);

        for ($offset = 0; $offset < $arrCount;) {
            $slice = array_slice($array, $offset, $maxPerPage, true);
            $result[$page] = $slice;

            ++$page;
            $offset += count($slice);
        }

        return $result;
    }

    /**
     * Removes an item using "dot" notation.
     *
     * @param string $key
     *
     * @return array
     */
    public function remove($key)
    {
        $array = &$this->array;

        if (is_null($key)) {
            return $array;
        }

        $keys = explode('.', $this->escapedDotKeyToUnderscore($key));

        while (count($keys) > 1) {
            $key = $this->underscoreDotKeyToDot(array_shift($keys));

            if (isset($array[$key]) === false || is_array($array[$key]) === false) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        unset($array[$this->underscoreDotKeyToDot(array_shift($keys))]);

        return $this->array;
    }

    /**
     * Sets an item using "dot" notation.
     * You can to escape a dot in a key surrendering with brackets: "[.]".
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public function set($key, $value)
    {
        $array = &$this->array;

        if (is_null($key)) {
            return $array;
        }

        $keys = explode('.', $this->escapedDotKeyToUnderscore($key));

        while (count($keys) > 1) {
            $key = $this->underscoreDotKeyToDot(array_shift($keys));

            if (isset($array[$key]) === false || is_array($array[$key]) === false) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[$this->underscoreDotKeyToDot(array_shift($keys))] = $value;

        return $this->array;
    }

    /**
     * Sets the working array.
     *
     * @param array $array
     *
     * @return \Yosymfony\Spress\Core\Support\ArrayWrapper This instance.
     */
    public function setArray(array $array)
    {
        $this->array = $array;

        return $this;
    }

    /**
     * Sorts the array (ascendant by default).
     *
     * @param string   $key      Element to sort using "dot" notation.
     *                           You can to escape a dot in a key surrendering with brackets: "[.]".
     * @param callable $callback Callback should be a function with
     *                           the following signature:
     *
     * ```php
     * function($element1, $element2)
     * {
     *     // returns -1, 0 or 1
     * }
     * ```
     *
     * @return array
     */
    public function sort($key = null, callable $callback = null)
    {
        $elements = is_null($key) === true ? $this->array : $this->get($key);

        $sortCallback = is_null($callback) === false ? $callback : function ($element1, $element2) {
            if ($element1 == $element2) {
                return 0;
            }

            return ($element1 < $element2) ? -1 : 1;
        };

        uasort($elements, $sortCallback);

        return $elements;
    }

    /**
     * Sort the array sing the given callback.
     *
     * @param callable $callback Callback should be a function with
     *                           the following signature:
     *
     * ```php
     * function($key, $value)
     * {
     *     // return $processedValue;
     * }
     * ```
     * @param string $key          Element to sort using "dot" notation.
     *                             You can to escape a dot in a key surrendering with brackets: "[.]".
     * @param int    $options      See sort_flags at http://php.net/manual/es/function.sort.php
     * @param bool   $isDescending
     *
     * @return array
     */
    public function sortBy(callable $callback, $key = null, $options = SORT_REGULAR, $isDescending = false)
    {
        $elements = [];
        $sourceElements = is_null($key) === true ? $this->array : $this->get($key);

        foreach ($sourceElements as $key => $value) {
            $elements[$key] = $callback($key, $value);
        }

        $isDescending ? arsort($elements, $options)
                    : asort($elements, $options);

        foreach (array_keys($elements) as $key) {
            $elements[$key] = $sourceElements[$key];
        }

        return $elements;
    }

    /**
     * Filter using the given callback function.
     *
     * @param callable $filter The filter function should be a function with the
     *                         following signature:
     *
     * ```php
     * function($key, $value)
     * {
     *     // returns true if the value is matching your criteria.
     * }
     * ```
     *
     * @return array
     */
    public function where(callable $filter)
    {
        $filtered = [];

        foreach ($this->array as $key => $value) {
            if ($filter($key, $value) === true) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    protected function unescapeDotKey($key)
    {
        return str_replace('[.]', '.', $key);
    }

    protected function escapedDotKeyToUnderscore($key)
    {
        return str_replace('[.]', '__dot__', $key);
    }

    protected function underscoreDotKeyToDot($key)
    {
        return str_replace('__dot__', '.', $key);
    }
}
