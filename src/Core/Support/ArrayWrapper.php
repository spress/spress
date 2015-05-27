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

    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    /**
     * Add an element using "dot" notation if doesn't exists
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
     * Get a value from a deeply nested array using "dot" notation
     *
     * e.g: $a->get('site.data')
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $array = $this->array;

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $step) {
            if (is_array($array) === false || array_key_exists($step, $array) === false) {
                return $default;
            }

            $array = $array[$step];
        }

        return $array;
    }

    /**
     * Get the working array
     *
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * Check if an item exists in using "dot" notation
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

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) === false || array_key_exists($segment, $array) === false) {
                return false;
            }

            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Paginate the array
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

        for ($offset = 0; $offset < count($array);) {
            $slice = array_slice($array, $offset, $maxPerPage, true);
            $result[$page] = $slice;

            $page++;
            $offset += count($slice);
        }

        return $result;
    }

    /**
     * Set an item using "dot" notation
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

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (isset($array[$key]) === false || is_array($array[$key]) === false) {
                $array[$key] = [];
            }

            $array = & $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $this->array;
    }

    /**
     * Set the working array
     *
     * @param array $array
     */
    public function setArray(array $array)
    {
        $this->array = $array;
    }

    /**
     * Filter using the given callback
     *
     * @param callable $callback
     *
     * @return array
     */
    public function where(callable $callback)
    {
        $filtered = [];

        foreach ($this->array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }
}
