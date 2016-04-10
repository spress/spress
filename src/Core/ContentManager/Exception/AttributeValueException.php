<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Exception;

/**
 * Exception class throw when the type or value of an attribute or option
 * is invalid.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class AttributeValueException extends ContentException
{
    protected $attribute;

    /**
     * Constructor.
     *
     * @param string    $message   The exception message.
     * @param string    $attribute The name of the attribute.
     * @param string    $id        The identifier of the content where the exception was generated.
     * @param Exception $previous  The previous exception.
     */
    public function __construct($message, $attribute = null, $id = null, \Exception $previous = null)
    {
        $this->attribute = $attribute;

        parent::__construct($message, $id, $previous);
    }

    public function getAttribute()
    {
        return $this->attribute;
    }

    public function setAttribute($attribute)
    {
        return $this->attribute = $attribute;
    }

    protected function updateRepr()
    {
        $this->message = $this->rawMessage;

        $dot = false;

        if (substr($this->message, -1) === '.') {
            $this->message = substr($this->message, 0, -1);
            $dot = true;
        }

        if (empty($this->id) === false) {
            $this->message .= sprintf(' in %s', json_encode($this->id));
        }

        if (empty($this->attribute) === false) {
            $this->message .= sprintf(' at key "%s"', $this->attribute);
        }

        if ($dot) {
            $this->message .= '.';
        }
    }
}
