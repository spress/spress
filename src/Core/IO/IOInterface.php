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
 * IO interface.
 *
 * Based on https://github.com/composer/composer/blob/master/src/Composer/IO/IOInterface.php
 * from François Pluchino <francois.pluchino@opendisplay.com>
 *
 * @author Victor Puertas
 */
interface IOInterface
{
    /**
     * Is this input means interactive?
     *
     * @return bool
     */
    public function isInteractive();

    /**
     * Is this output verbose?
     *
     * @return bool
     */
    public function isVerbose();

    /**
     * Is the output very verbose?
     *
     * @return bool
     */
    public function isVeryVerbose();

    /**
     * Is the output in debug verbosity?
     *
     * @return bool
     */
    public function isDebug();

    /**
     * Is this output decorated?
     *
     * @return bool
     */
    public function isDecorated();

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages The message as an array of lines or a single string
     * @param bool         $newline  Whether to add a newline or not
     */
    public function write($messages, $newline = true);

    /**
     * Asks a question to the user.
     *
     * @param string|array $question The question to ask
     * @param string       $default  The default answer if none is given by the user
     *
     * @return string The user answer
     *
     * @throws \RuntimeException If there is no data to read in the input stream
     */
    public function ask($question, $default = null);

    /**
     * Asks a confirmation to the user.
     *
     * The question will be asked until the user answers by nothing, yes, or no.
     *
     * @param string|array $question The question to ask
     * @param bool         $default  The default answer if the user enters nothing
     *
     * @return bool true if the user has confirmed, false otherwise
     */
    public function askConfirmation($question, $default = true);

    /**
     * Asks for a value and validates the response.
     *
     * The validator receives the data to validate. It must return the
     * validated data when the data is valid and throw an exception
     * otherwise.
     *
     * @param string|array $question  The question to ask
     * @param callback     $validator A PHP callback
     * @param bool|int     $attempts  Max number of times to ask before giving up (false by default, which means infinite)
     * @param string       $default   The default answer if none is given by the user
     *
     * @return mixed
     *
     * @throws \Exception When any of the validators return an error
     */
    public function askAndValidate($question, callable $validator, $attempts = false, $default = null);

    /**
     * Asks a question to the user and hide the answer.
     *
     * @param string $question The question to ask
     * @param bool   $fallback In case the response can not be hidden, whether to fallback on non-hidden question or not
     *
     * @return string The answer
     */
    public function askAndHideAnswer($question, $fallback);

    /**
     * Asks for a value, hide and validates the response.
     *
     * The validator receives the data to validate. It must return the
     * validated data when the data is valid and throw an exception
     * otherwise.
     *
     * @param string|array $question  The question to ask
     * @param callback     $validator A PHP callback
     * @param bool|int     $attempts  Max number of times to ask before giving up (false by default, which means infinite)
     * @param bool         $fallback  In case the response can not be hidden, whether to fallback on non-hidden question or not
     *
     * @return mixed
     *
     * @throws \Exception When any of the validators return an error
     */
    public function askHiddenResponseAndValidate($question, callable $validator, $attempts = false, $fallback);
}
