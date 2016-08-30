<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\ContentManager\Renderizer\Exception;

/**
 * Exception class throw when an error occurs while
 * the content is rendering.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class RenderException extends \RuntimeException
{
    protected $rawMessage;
    protected $id;

    /**
     * Constructor.
     *
     * @param string    $message  The exception message
     * @param string    $id       The identifier of the content where the exception was created
     * @param Exception $previous The previous exception
     */
    public function __construct($message, $id = null, \Exception $previous = null)
    {
        $this->rawMessage = $message;
        $this->id = $id;

        $this->updateRepr();

        parent::__construct($this->message, 0, $previous);
    }

    /**
     * Sets the identifier where the exception was created.
     *
     * @param string $id The identifier of the content
     */
    public function setId($id)
    {
        $this->id = $id;

        $this->updateRepr();
    }

    /**
     * Gets the identifier where the exception was created.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    private function updateRepr()
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

        if ($dot) {
            $this->message .= '.';
        }
    }
}
