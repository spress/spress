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
     * {@inheritDoc}
     */
    public function isVerbose()
    {
        return false;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isVeryVerbose()
    {
        return false;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isDebug()
    {
        return false;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isDecorated()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function write($messages, $newline = true)
    {
        // do nothing
    }
    
    /**
     * {@inheritDoc}
     */
    public function ask($question, $default = null)
    {
        return $default;
    }
    
    /**
     * {@inheritDoc}
     */
    public function askConfirmation($question, $default = true)
    {
        return $default;
    }
    
    /**
     * {@inheritDoc}
     */
    public function askAndValidate($question, callable $validator, $attempts = false, $default = null)
    {
        return $default;
    }
    
    /**
     * {@inheritDoc}
     */
    public function askAndHideAnswer($question, $fallback = true)
    {
            return null;
    }
    
    public function askHiddenResponseAndValidate($question, callable $validator, $attempts = false, $fallback)
    {
        return null;
    }
}
