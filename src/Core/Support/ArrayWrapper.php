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
  * Array wrapper for working with arrays.
  *
  * Based on https://github.com/laravel/framework/blob/5.0/src/Illuminate/Support/Arr.php
  *
  * @author Victor Puertas <vpgugr@gmail.com>
  */
class ArrayWrapper
{
    protected $array;

    public function __construct(array $array)
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
