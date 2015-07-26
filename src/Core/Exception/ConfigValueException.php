<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Exception;

/**
 * Exception class throw when the type or value of configuration file
 * is not invalid.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConfigValueException extends \DomainException
{
    protected $rawMessage;
    protected $key;
    protected $filename;

    /**
     * Constructor.
     *
     * @param string    $message
     * @param string    $key
     * @param string    $filename
     * @param Exception $previous
     */
    public function __construct($message, $key = null, $filename = null, \Exception $previous = null)
    {
        $this->rawMessage = $message;
        $this->filename = $filename;
        $this->key = $key;

        $this->updateRepr();

        parent::__construct($this->message, 0, $previous);
    }

    public function setKey($name)
    {
        $this->key = $name;

        $this->updateRepr();
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;

        $this->updateRepr();
    }

    public function getFilename()
    {
        return $this->filename;
    }

    private function updateRepr()
    {
        $this->message = $this->rawMessage;

        $dot = false;

        if (substr($this->message, -1) === '.') {
            $this->message = substr($this->message, 0, -1);
            $dot = true;
        }

        if (empty($this->filename) === false) {
            $this->message .= sprintf(' in %s', json_encode($this->filename));
        }

        if (empty($this->key) === false) {
            $this->message .= sprintf(' at key "%s"', $this->key);
        }

        if ($dot) {
            $this->message .= '.';
        }
    }
}
