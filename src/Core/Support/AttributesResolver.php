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

use Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException;
use Yosymfony\Spress\Core\ContentManager\Exception\MissingAttributeException;

/**
 * It allows you to create an attribute or option set with required,
 * defaults, validation attributes.
 *
 * Inspired by Symfony OptionsResolver component:
 * http://symfony.com/doc/current/components/options_resolver.html.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class AttributesResolver
{
    private $defaults = [];
    private $types = [];
    private $requires = [];
    private $notNullables = [];
    private $validators = [];
    private $resolved = [];

    /**
     * Sets the default value of a given attribute.
     *
     * @param string $attribute The name of the attribute.
     * @param mixed  $value     The default value of the attribute.
     * @param string $type      The accepted type. Any type for which a corresponding is_<type>() function exists is
     *                          acceptable.
     * @param bool   $required  Is that attribute required?
     * @param bool   $nullable  Is that attribute nullable?
     *
     * @return \Yosymfony\Spress\Core\Support\AttributesResolver This instance.
     */
    public function setDefault($attribute, $value, $type = null, $required = false, $nullable = false)
    {
        $this->defaults[$attribute] = $value;

        if (empty($type) === false) {
            $this->types[$attribute] = $type;
        }

        if ($required === true) {
            $this->requires[] = $attribute;
        }

        if ($nullable === false) {
            $this->notNullables[] = $attribute;
        }

        return $this;
    }

    /**
     * Sets the validator for an atribute.
     *
     * @param string   $attribute The name of the attribute.
     * @param \Closure $validator The validator should be a closure with the following signature:
     *
     * ```php
     * function ($value) {
     *     // ...
     * }
     * ```
     *
     * @return \Yosymfony\Spress\Core\Support\AttributesResolver This instance.
     */
    public function setValidator($attribute, \Closure $validator)
    {
        $this->validators[$attribute] = $validator;

        return $this;
    }

    /**
     * Return whether a default value is set for an attribute.
     *
     * @return bool
     */
    public function hasDefault($attribute)
    {
        return array_key_exists($attribute, $this->defaults);
    }

    /**
     * Merges options with the default values and validates them.
     * If an attribute is marked as nullable the validate function never will be invoked.
     *
     * @param array $attributes
     *
     * @return array The merged and validated options
     *
     * @throws \Yosymfony\Spress\Core\ContentManager\Exception\AttributeValueException   If the attributes don't validate the rules
     * @throws \Yosymfony\Spress\Core\ContentManager\Exception\MissingAttributeException If a required values is missing
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

        foreach ($clone->validators as $attribute => $validator) {
            if (is_null($clone->resolved[$attribute]) === false && $validator($clone->resolved[$attribute]) === false) {
                throw new AttributeValueException(
                        sprintf('Invalid value.', $attribute),
                        $attribute
                    );
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
     * Remove all attributes.
     */
    public function clear()
    {
        $this->defaults = [];
        $this->types = [];
        $this->requires = [];
        $this->notNullables = [];

        return $this;
    }

    /**
     * Removes the attributes with the given name.
     *
     * @param string|string[] One or more attributes.
     *
     * @return \Yosymfony\Spress\Core\Support\AttributesResolver This instance.
     */
    public function remove($attributes)
    {
        foreach ((array) $attributes as $attribute) {
            unset($this->defaults[$attribute],
                $this->type[$attribute],
                $this->requires[$attribute],
                $this->notNullables[$attribute]);
        }

        return $this;
    }

    /**
     * Return the number of set options.
     *
     * @return int
     */
    public function count()
    {
        return count($this->defaults);
    }
}
