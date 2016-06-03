<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Plugin\Environment;

/**
 * Represents an incorrect command name.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class CommandNotFoundException extends \LogicException
{
    /**
     * Constructor.
     *
     * @param string    $message      Exception message to throw.
     * @param int       $code         Exception code.
     * @param Exception $previous     previous exception used for the exception chaining.
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
