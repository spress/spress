<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\IO;

/**
 * Null implementation. Do nothing.
 *
 * @author Victor Puertas
 */
class NullIO implements IOInterface
{
    public function isInteractive()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = true, $verbosity = self::VERBOSITY_NORMAL)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function overwrite($messages, $newline = true, $verbosity = self::VERBOSITY_NORMAL)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function ask($question, $default = null)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askConfirmation($question, $default = true)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askAndValidate($question, callable $validator, $attempts = false, $default = null)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function askAndHideAnswer($question, $fallback = true)
    {
        return;
    }

    public function askHiddenResponseAndValidate($question, callable $validator, $attempts = false, $fallback = true)
    {
        return;
    }
}
