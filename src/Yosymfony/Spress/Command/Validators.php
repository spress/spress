<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Command;

/**
 * Validators
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Validators
{
    /**
     * Validator for the name of a plugin
     *
     * @param string $name
     *
     * @return string
     */
    public static function validatePluginName($name)
    {
        if (!preg_match('{^[a-z0-9_.-]+/[a-z0-9_.-]+$}', $name)) {
            throw new \InvalidArgumentException(
                'The plugin name '.$name.' is invalid, it should be lowercase '.
                'and have a vendor name, a forward slash, and a package name. e.g: yosymfony/myplugin.');
        }

        return $name;
    }

    /**
     * Validator for a namespace
     *
     * @param string $namespace
     *
     * @return string
     */
    public static function validateNamespace($namespace)
    {
        if (0 == strlen($namespace)) {
            return '';
        }

        // The namespace:
        $namespace = strtr($namespace, '/', '\\');

        if (!preg_match('/^(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\?)+$/', $namespace)) {
            throw new \InvalidArgumentException('The namespace contains invalid characters.');
        }

        // PHP reserved words:
        $reservedWords = self::getPhpReservedWords();

        foreach (explode('\\', $namespace) as $word) {
            if (in_array(strtolower($word), $reservedWords)) {
                throw new \InvalidArgumentException(sprintf('The namespace cannot contain PHP reserved words ("%s").', $word));
            }
        }

        return $namespace;
    }

    /**
     * Validator for the title of a post
     *
     * @param string $title
     *
     * @return string
     */
    public static function validatePostTitle($title)
    {
        if (0 == strlen($title)) {
            throw new \InvalidArgumentException('The title of a post should not be empty.');
        }

        return $title;
    }

    /**
     * Validator for a Email
     *
     * @param string $email
     *
     * @return string
     */
    public static function validateEmail($email)
    {
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('The Email "%s" is invalid.', $email));
        }

        return $email;
    }

    /**
     * List of PHP reserved words
     *
     * @return array
     */
    public static function getPhpReservedWords()
    {
        return [
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'case',
            'catch',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'do',
            'else',
            'elseif',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'extends',
            'final',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'interface',
            'instanceof',
            'namespace',
            'new',
            'or',
            'private',
            'protected',
            'public',
            'static',
            'switch',
            'throw',
            'try',
            'use',
            'var',
            'while',
            'xor',
            '__CLASS__',
            '__DIR__',
            '__FILE__',
            '__LINE__',
            '__FUNCTION__',
            '__METHOD__',
            '__NAMESPACE__',
            'die',
            'echo',
            'empty',
            'exit',
            'eval',
            'include',
            'include_once',
            'isset',
            'list',
            'require',
            'require_once',
            'return',
            'print',
            'unset',
        ];
    }
}
