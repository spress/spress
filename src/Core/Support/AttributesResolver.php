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

use Yosymfony\Spress\Core\Exception\AttributeValueException;
use Yosymfony\Spress\Core\Exception\MissingAttributeException;

/**
  * It allows you to create an attribute set with required, defaults, validation
  * attributes. An attribute is like an option.
  *
  * Inspired by Symfony OptionsResolver component: http://symfony.com/doc/current/components/options_resolver.html
  *
  * @author Victor Puertas <vpgugr@gmail.com>
  */
class AttributesResolver
{
    private $defaults = [];
    private $types = [];
    private $requires = [];
    private $notNullables = [];
    private $resolved = [];

    /**
     * Sets the default value of a given attribute
     *
     * @param string $name     The name of the attribute
     * @param mixed  $value    The default value of the attribute
     * @param string $type     The accepted type. Any type for which a corresponding is_<type>() function exists is
     *                         acceptable
     * @param bool   $required Is that attribute required?
     * @param bool   $nullable Is that attribute nullable?
     */
    public function setDefault($name, $value, $type = null, $required = false, $nullable = false)
    {
        $this->defaults[$name] = $value;

        if (empty($type) === false) {
            $this->types[$name] = $type;
        }

        if ($required === true) {
            $this->requires[] = $name;
        }

        if ($nullable === false) {
            $this->notNullables[] = $name;
        }

        return $this;
    }

    /**
     * Return whether a default value is set for an attribute
     *
     * @return bool
     */
    public function hasDefault($attribute)
    {
        return array_key_exists($attribute, $this->defaults);
    }

    /**
     * Merges options with the default values and validates them.
     *
     * @param array $attributes
     *
     * @return array The merged and validated options
     *
     * @throws \Yosymfony\Spress\Core\Exception\AttributeValueException   If the attributes don't validate the rules
     * @throws \Yosymfony\Spress\Core\Exception\MissingAttributeException If a required values is missing
     */
    public function resolve(array $attributes)
    {
        $clone = clone $this;

        $clone->resolved = array_replace($clone->defaults, $attributes);

        foreach ($clone->types as $attribute => $type) {
            if (function_exists($isFunction = 'is_'.$type) === true) {
                if (is_null($clone->resolved[$attribute]) === false && $isFunction($clone->resolved[$attribute]) === false) {
                    throw new AttributeValueException(
                        sprintf('Invalid type of value. Expected "%s".', $type),
                        $attribute
                    );
                }
            }
        }

        foreach ($clone->notNullables as $attribute) {
            if (is_null($clone->resolved[$attribute]) === true) {
                throw new AttributeValueException('Unexpected null value.', $attribute);
            }
        }

        foreach ($clone->requires as $attribute) {
            if (array_key_exists($attribute, $attributes) === false) {
                throw new MissingAttributeException(sprintf('Missing attribute or option "%s".', $attribute));
            }
        }

        return $clone->resolved;
    }

    /**
     * Remove all attributes
     */
    public function clear()
    {
        $this->defaults = [];
        $this->type = [];
        $this->requires = [];
        $this->nullables = [];

        return $this;
    }

    /**
     * Removes the attributes with the given name
     *
     * @param string|string[] One or more attributes
     */
    public function remove($attributes)
    {
        foreach ((array) $attributes as $attribute) {
            unset($this->defaults[$attribute],
                $this->type[$attribute],
                $this->requires[$attribute],
                $this->nullables[$attribute]);
        }

        return $this;
    }

    /**
     * Return the number of set options
     *
     * @return int
     */
    public function count()
    {
        return count($this->defaults);
    }
}
